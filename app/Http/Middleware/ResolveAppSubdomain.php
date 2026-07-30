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
        $request->attributes->set('app_subdomain', self::resolve($request));

        return $next($request);
    }
}
