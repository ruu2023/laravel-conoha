<?php

use App\Http\Middleware\ResolveAppSubdomain;
use Illuminate\Support\Facades\Route;

// Each app's routes live under a URI prefix matching its name; the request's
// actual path never has this prefix — ResolveAppSubdomain's middleware adds
// it based on X-App-Subdomain before routing happens. This registers real,
// static routes at boot (route:cache-safe) instead of branching on the
// header here, since boot only runs once per Application, not per request.
// The list of apps comes from ResolveAppSubdomain::apps() (the routes/apps/
// directory itself), so there's nothing to keep in sync when adding one.
foreach (ResolveAppSubdomain::apps() as $app) {
    Route::prefix($app)->group(__DIR__ . "/apps/{$app}.php");
}
