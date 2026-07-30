<?php

namespace App\Routing;

use App\Http\Middleware\ResolveAppSubdomain;
use Illuminate\Routing\UrlGenerator;

/**
 * Each mini app's routes are registered under a URI prefix matching its
 * name (see routes/web.php's Route::prefix($app) loop) so that same-path
 * routes across apps don't collide — but ResolveAppSubdomain only strips
 * that prefix on the way in (incoming request path). Left alone, route()
 * and action() build URLs straight from the prefixed route definition, so
 * every generated link would carry a stray leading segment (e.g.
 * `/post/1` instead of `/1`) that 404s for the client, who only ever sees
 * the unprefixed path. This mirrors that stripping on the way out, so any
 * app's views/controllers can call route()/action() normally.
 *
 * Absolute URLs need a second correction: the Cloudflare Worker in front
 * of this app always rewrites Host to `laravel.ruu-dev.com` before
 * forwarding, so `$request->getHost()` — what route($name, $params, true)
 * builds the domain from — is never the domain the visitor actually used.
 * rewriteHost() substitutes the resolved subdomain back in, so e.g. a
 * `post.ruu-dev.com` visitor gets `post.ruu-dev.com` links, not
 * `laravel.ruu-dev.com` ones. Locally there's no Worker, so Host is
 * already correct (e.g. `post.localhost`) and this is a no-op.
 */
class SubdomainAwareUrlGenerator extends UrlGenerator
{
    public function route($name, $parameters = [], $absolute = true)
    {
        return $this->fixAppUrl(parent::route($name, $parameters, $absolute));
    }

    public function action($action, $parameters = [], $absolute = true)
    {
        return $this->fixAppUrl(parent::action($action, $parameters, $absolute));
    }

    protected function fixAppUrl(string $url): string
    {
        $subdomain = $this->getRequest()->appSubdomain();

        if (! $subdomain) {
            return $url;
        }

        return $this->rewriteHost($this->stripAppPrefix($url, $subdomain), $subdomain);
    }

    protected function stripAppPrefix(string $url, string $subdomain): string
    {
        $stripped = preg_replace(
            '#(^https?://[^/]+)?/'.preg_quote($subdomain, '#').'(?=/|$)#',
            '$1',
            $url,
            1,
        );

        return $stripped === '' ? '/' : $stripped;
    }

    protected function rewriteHost(string $url, string $subdomain): string
    {
        $host = $this->getRequest()->getHost();
        $defaultApp = ResolveAppSubdomain::DEFAULT_APP;

        // Host is only ever the Worker's stand-in ("laravel.<root>") when
        // it's been rewritten; anywhere else (local *.localhost, or a
        // genuine direct request to laravel.ruu-dev.com) it's already the
        // real one, so leave it alone.
        if (! str_starts_with($host, "{$defaultApp}.")) {
            return $url;
        }

        $root = substr($host, strlen($defaultApp) + 1);

        return preg_replace(
            '#^(https?://)'.preg_quote($host, '#').'#',
            '$1'.$subdomain.'.'.$root,
            $url,
            1,
        );
    }
}
