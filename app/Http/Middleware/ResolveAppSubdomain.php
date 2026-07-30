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
 * unprefixed path. Resolving via a request header rather than a
 * conditionally-loaded route file (the first version of this) matters
 * because route registration happens once per Application boot, and only
 * traditional per-request PHP processes (PHP-FPM, `artisan serve`) rebuild
 * the Application per request — tests and Octane reuse it, so a header read
 * at boot time would silently freeze at whatever request built the app.
 *
 * To publish a new mini app: add its subdomain to APPS below and create a
 * matching routes/apps/{subdomain}.php file.
 */
class ResolveAppSubdomain
{
    private const HEADER = 'X-App-Subdomain';

    /**
     * Whitelist of subdomains this app is allowed to serve. Anything else
     * (including a spoofed header value) resolves to null and 404s, since
     * routes/web.php only loads a route file for a whitelisted key.
     */
    public const APPS = ['laravel', 'memo', 'dockerfiles'];

    /**
     * Used when the header is absent entirely (e.g. a direct request that
     * didn't go through the Worker) so laravel.ruu-dev.com keeps working.
     */
    public const DEFAULT_APP = 'laravel';

    public static function resolve(Request $request): ?string
    {
        $header = $request->header(self::HEADER);

        if ($header !== null && $header !== '') {
            return in_array($header, self::APPS, true) ? $header : null;
        }

        // Local convenience: visit e.g. http://memo.localhost directly instead
        // of setting the header by hand. *.localhost resolves to loopback with
        // no /etc/hosts edit, and this never applies outside `local` — in
        // production the Host is always rewritten to laravel.ruu-dev.com by
        // the Worker, so a spoofed Host header here can't do anything.
        if (app()->environment('local') && preg_match("/^([a-z0-9-]+)\.localhost$/i", $request->getHost(), $matches)) {
            return in_array($matches[1], self::APPS, true) ? $matches[1] : null;
        }

        return self::DEFAULT_APP;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = self::resolve($request);

        abort_if($subdomain === null, 404);

        $prefixed = Request::create(
            '/'.$subdomain.$request->getRequestUri(),
            $request->getMethod(),
            $request->query->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            $request->getContent(),
        );
        $prefixed->headers->replace($request->headers->all());
        $prefixed->attributes->set('app_subdomain', $subdomain);

        return $next($prefixed);
    }
}
