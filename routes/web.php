<?php

use App\Http\Middleware\ResolveAppSubdomain;

// Which mini app handles this request is decided by the X-App-Subdomain
// header (see ResolveAppSubdomain), not the URL path, so route caching is
// intentionally not used here — see .github/workflows/deploy.yml.
$subdomain = ResolveAppSubdomain::resolve(request());

if ($subdomain !== null) {
    require __DIR__."/apps/{$subdomain}.php";
}
