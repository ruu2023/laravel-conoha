<?php

use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Shared Google-login hub for techpulse/zundamon (see
// config/restricted_apps.php). Logging in here, not on techpulse/zundamon
// themselves, means only one OAuth redirect URI needs registering with
// Google instead of one per gated app.
Route::get('/', function () {
    if (! Auth::check()) {
        return view('login.login');
    }

    // Links to the gated apps for the post-login page. Every app lives
    // under the same domain as a path (/techpulse, /zundamon), so route()
    // already returns the correct public URL with no cross-app host
    // juggling needed.
    $appUrls = collect(['techpulse', 'zundamon'])
        ->mapWithKeys(fn ($app) => [$app => route("{$app}.home")])
        ->all();

    return view('login.logged-in', [
        'email' => Auth::user()->email,
        'appUrls' => $appUrls,
    ]);
})->name('login.home');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('login.auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('login.auth.google.callback');
