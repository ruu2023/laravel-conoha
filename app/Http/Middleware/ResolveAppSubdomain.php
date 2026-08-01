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
}
