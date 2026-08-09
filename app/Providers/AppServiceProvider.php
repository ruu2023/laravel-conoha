<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
