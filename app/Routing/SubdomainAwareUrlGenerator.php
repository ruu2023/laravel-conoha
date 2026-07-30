<?php

namespace App\Routing;

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
 */
class SubdomainAwareUrlGenerator extends UrlGenerator
{
    public function route($name, $parameters = [], $absolute = true)
    {
        return $this->stripAppPrefix(parent::route($name, $parameters, $absolute));
    }

    public function action($action, $parameters = [], $absolute = true)
    {
        return $this->stripAppPrefix(parent::action($action, $parameters, $absolute));
    }

    protected function stripAppPrefix(string $url): string
    {
        $subdomain = $this->getRequest()->appSubdomain();

        if (! $subdomain) {
            return $url;
        }

        $stripped = preg_replace(
            '#(^https?://[^/]+)?/'.preg_quote($subdomain, '#').'(?=/|$)#',
            '$1',
            $url,
            1,
        );

        return $stripped === '' ? '/' : $stripped;
    }
}
