<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = "app";

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
        ];
    }

    /**
     * Inertia's default resolver builds the "url" page prop straight from
     * $request->getRequestUri() — but by the time this runs, that's the
     * *rewritten* request ResolveAppSubdomain produced (path prefixed with
     * /{app}, e.g. /root/dashboard instead of the /dashboard the browser
     * actually requested). Left alone, every Inertia page would report the
     * wrong current URL. This mirrors the same prefix-stripping
     * App\Routing\SubdomainAwareUrlGenerator already does for route()/
     * action() output, just for the incoming URL instead of a generated one.
     */
    public function urlResolver(): Closure
    {
        return function (Request $request) {
            $subdomain = $request->appSubdomain();

            if (! $subdomain) {
                return $request->getRequestUri();
            }

            $stripped = preg_replace(
                "#^/".preg_quote($subdomain, "#")."(?=/|$)#",
                "",
                $request->getRequestUri(),
                1,
            );

            return $stripped === "" ? "/" : $stripped;
        };
    }
}
