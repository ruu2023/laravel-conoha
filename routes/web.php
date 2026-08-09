<?php

use Illuminate\Support\Facades\Route;

// Every mini app's routes live under a URI prefix matching its name, and
// that prefix is the real public URL (e.g. ruu-dev.com/memo/...) — there's
// no rewriting between what the browser sends and what gets routed. This
// registers real, static routes at boot (route:cache-safe), never branching
// on the request itself: Laravel's route registration
// (this file) runs once per Application boot, not once per request — a
// classic PHP-FPM process only rebuilds the Application per request, but
// tests and Octane reuse it across many requests, so anything that must
// vary per request (there's nothing like that left now) can't be decided
// here.
//
// The app list comes straight from the routes/apps/ directory (globbed
// below) so there's nothing to keep in sync when adding one — see
// docs/subdomain-routing.md for the history of why a hand-maintained
// whitelist here is a trap (a stale one previously crashed every app's
// boot when it listed a file that didn't exist).
$apps = collect(glob(base_path("routes/apps/*.php")))
    ->map(fn ($path) => pathinfo($path, PATHINFO_FILENAME))
    ->diff(config("apps.disabled", []))
    ->diff(config("app.disabled_apps", []))
    ->sort()
    ->values();

foreach ($apps as $app) {
    // "root" is the apex app and stays unprefixed, so ruu-dev.com/ (and
    // /dashboard, etc.) serve it directly instead of via /root/....
    if ($app === "root") {
        Route::group([], __DIR__ . "/apps/{$app}.php");
    } else {
        Route::prefix($app)->group(__DIR__ . "/apps/{$app}.php");
    }
}
