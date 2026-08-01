<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves which mini app should handle the current request, based on the
 * `X-App-Subdomain` header set by the Cloudflare Worker in front of ConoHa WING
 * (the Worker rewrites Host to laravel.ruu-dev.com and forwards the originally
 * requested subdomain in this header instead).
 *
 * Each app's routes are registered at boot under a URI prefix matching its
 * name (see routes/web.php), so this middleware rewrites the incoming
 * request's path to add that prefix before routing happens — the prefix
 * never leaks to the browser, since the client only ever sees the
 * unprefixed path. The reverse direction (route()/action() output) is
 * handled separately by App\Routing\SubdomainAwareUrlGenerator. Resolving
 * via a request header rather than a conditionally-loaded route file
 * (the first version of this) matters
 * because route registration happens once per Application boot, and only
 * traditional per-request PHP processes (PHP-FPM, `artisan serve`) rebuild
 * the Application per request — tests and Octane reuse it, so a header read
 * at boot time would silently freeze at whatever request built the app.
 *
 * To publish a new mini app: create routes/apps/{subdomain}.php — that's
 * the only step. apps() below discovers it automatically, so there is no
 * separate whitelist to keep in sync (a whitelist entry with no matching
 * file previously crashed *every* subdomain's boot, since routes/web.php
 * required a file that didn't exist).
 */
class ResolveAppSubdomain
{
    private const HEADER = "X-App-Subdomain";

    /**
     * Used when the header is absent entirely (e.g. a direct request that
     * didn't go through the Worker) so laravel.ruu-dev.com keeps working.
     */
    public const DEFAULT_APP = "laravel";

    /**
     * Whitelist of subdomains this app is allowed to serve, derived from
     * the routes/apps/*.php files actually present — the same list
     * routes/web.php loops over to register routes. Anything else
     * (including a spoofed header value) resolves to null and 404s. A
     * single source of truth (the filesystem) instead of a hand-maintained
     * const means the whitelist can never list an app whose route file
     * doesn't exist, which is what broke every subdomain last time.
     *
     * config('apps.disabled') (version-controlled — commit & push to
     * toggle) and config('app.disabled_apps') (env DISABLED_APPS, an
     * SSH-only emergency toggle for when a deploy can't wait) are both
     * subtracted from this list, so a mini app can be taken offline —
     * cleanly 404ing, without affecting any other app — without deleting
     * its route file.
     */
    public static function apps(): array
    {
        // Deliberately not cached in a static var: PHP-FPM workers are
        // reused across many requests (only the Laravel Application is
        // rebuilt per request, not the PHP process), so a static cache
        // here would keep serving a stale list — e.g. config/apps.php's
        // disabled entries wouldn't take effect until that worker happened
        // to recycle. glob() over a handful of files is cheap enough that
        // recomputing this every call isn't worth that risk.
        return collect(glob(base_path("routes/apps/*.php")))
            ->map(fn($path) => pathinfo($path, PATHINFO_FILENAME))
            ->diff(config("apps.disabled", []))
            ->diff(config("app.disabled_apps", []))
            ->sort()
            ->values()
            ->all();
    }

    public static function resolve(Request $request): ?string
    {
        $header = $request->header(self::HEADER);

        if ($header !== null && $header !== "") {
            return in_array($header, self::apps(), true) ? $header : null;
        }

        // Local convenience: visit e.g. http://memo.localhost directly instead
        // of setting the header by hand. *.localhost resolves to loopback with
        // no /etc/hosts edit, and this never applies outside `local` — in
        // production the Host is always rewritten to laravel.ruu-dev.com by
        // the Worker, so a spoofed Host header here can't do anything.
        if (
            app()->environment("local") &&
            preg_match(
                "/^([a-z0-9-]+)\.localhost$/i",
                $request->getHost(),
                $matches,
            )
        ) {
            return in_array($matches[1], self::apps(), true) ? $matches[1] : null;
        }

        // The apex domain isn't behind the Worker (the wildcard DNS only
        // covers *.ruu-dev.com), so it never carries the header and its Host
        // arrives unmodified as "ruu-dev.com" — unlike every other app here,
        // which only ever sees Host rewritten to laravel.ruu-dev.com.
        if ($request->getHost() === "ruu-dev.com") {
            return in_array("root", self::apps(), true) ? "root" : null;
        }

        return self::DEFAULT_APP;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = self::resolve($request);

        abort_if($subdomain === null, 404);

        if (in_array($subdomain, config("restricted_apps.session_shared_apps", []), true)) {
            $this->widenSessionCookieToRootDomain($request);
        }

        $prefixed = Request::create(
            "/" . $subdomain . $request->getRequestUri(),
            $request->getMethod(),
            $request->query->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            $request->getContent(),
        );
        $prefixed->headers->replace($request->headers->all());
        $prefixed->attributes->set("app_subdomain", $subdomain);

        return $next($prefixed);
    }

    /**
     * The login hub (routes/apps/laravel.php) and the apps gated behind it
     * (techpulse, zundamon) share one Google-login session (see
     * config/restricted_apps.php), so their session cookie needs a domain
     * wide enough to cross all three hosts instead of the default per-host
     * one. Runs before StartSession (that's part of the 'web' middleware
     * group, layered inside this globally-appended one), so mutating
     * config('session.domain') here still takes effect.
     */
    private function widenSessionCookieToRootDomain(Request $request): void
    {
        $host = $request->getHost();

        if (str_starts_with($host, self::DEFAULT_APP . ".")) {
            // Worker-rewritten Host in production, e.g. "laravel.ruu-dev.com".
            config(["session.domain" => "." . substr($host, strlen(self::DEFAULT_APP) + 1)]);
        } elseif (
            app()->environment("local") &&
            ($host === "localhost" || str_ends_with($host, ".localhost"))
        ) {
            // Bare "localhost" is the login hub locally (no subdomain to
            // match DEFAULT_APP's "{app}." prefix against); "{app}.localhost"
            // is techpulse/zundamon. Widened for parity with production, but
            // note browsers treat "localhost" as a single-label eTLD and
            // won't actually honor cross-subdomain sharing for it — Google's
            // OAuth policy also only allows the insecure http callback on
            // exactly "localhost"/"127.0.0.1", so there's no local hostname
            // that satisfies both constraints at once. The shared-session
            // flow can only be fully exercised on the real *.ruu-dev.com
            // (https, not eTLD-restricted) — see PR/chat discussion. Locally,
            // verify the two halves separately: login completes, and
            // EnsureAllowedGoogleUser 403s without a session.
            config(["session.domain" => ".localhost"]);
        }
    }
}
