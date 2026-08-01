<?php

namespace App\Providers;

use App\Routing\SubdomainAwareUrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->extend("url", function (UrlGenerator $url, $app) {
            $generator = new SubdomainAwareUrlGenerator(
                $app["router"]->getRoutes(),
                $url->getRequest(),
                $app["config"]["app.asset_url"],
            );

            $generator->setSessionResolver(fn() => $app["session"] ?? null);
            $generator->setKeyResolver(
                fn() => [
                    $app["config"]["app.key"],
                    ...$app["config"]["app.previous_keys"] ?? [],
                ],
            );

            return $generator;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Request::macro("appSubdomain", function () {
            return $this->attributes->get("app_subdomain");
        });

        // ConoHa WING puts a local nginx between Cloudflare and PHP-FPM, so
        // the direct peer Laravel sees is nginx's own IP, not one of the
        // trusted Cloudflare ranges in config/cloudflare.php — trustProxies'
        // X-Forwarded-Proto handling never kicks in, and route()/url() fall
        // back to plain http even though every real request is https. This
        // broke the Google OAuth redirect_uri (GoogleAuthController), which
        // must exactly match what's registered in Google Cloud Console.
        // Production is https-only, so just force it outside `local`.
        if (! $this->app->environment("local")) {
            URL::forceScheme("https");
        }
    }
}
