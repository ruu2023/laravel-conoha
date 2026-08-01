<?php

use App\Http\Controllers\GoogleAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Legacy top-level host (laravel.ruu-dev.com) and the no-header fallback —
// repurposed as the shared Google-login hub for techpulse/zundamon (see
// config/restricted_apps.php). Logging in here, not on techpulse/zundamon
// themselves, means only one OAuth redirect URI needs registering with
// Google instead of one per gated subdomain.
Route::get('/', function (Request $request) {
    if (! Auth::check()) {
        return view('laravel.login');
    }

    // Absolute links to the gated apps for the post-login page. Mirrors
    // SubdomainAwareUrlGenerator::rewriteHost()'s host-prefix logic, since
    // this crosses into other apps' subdomains, which that generator
    // (deliberately) doesn't handle.
    $host = $request->getHost();
    $root = str_starts_with($host, 'laravel.') ? substr($host, strlen('laravel.')) : $host;
    $appUrls = collect(['techpulse', 'zundamon'])
        ->mapWithKeys(fn ($app) => [$app => $request->getScheme().'://'.$app.'.'.$root])
        ->all();

    return view('laravel.logged-in', [
        'email' => Auth::user()->email,
        'appUrls' => $appUrls,
    ]);
})->name('laravel.home');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('laravel.auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('laravel.auth.google.callback');
