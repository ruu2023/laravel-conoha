<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates techpulse/zundamon behind an existing Google-login session (see
 * config('restricted_apps.allowed_emails')). Logging in only happens on the
 * shared hub (routes/apps/laravel.php) — no session here, or a session for
 * an email that isn't allow-listed, both just get a 403. No redirect to
 * Google from here: these subdomains don't have their own OAuth callback
 * route registered with Google, only the hub does.
 *
 * Skipped entirely in `local`: the OAuth callback can only be registered
 * with Google for bare "localhost" (see GoogleAuthController/laravel.php),
 * and its session can't be shared to *.localhost (browsers treat
 * "localhost" as a single-label eTLD), so there's no way to reach an
 * authenticated state on techpulse.localhost/zundamon.localhost at all —
 * gating them locally would just make them permanently inaccessible.
 */
class EnsureAllowedGoogleUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local')) {
            return $next($request);
        }

        $user = Auth::user();

        abort_unless(
            $user && in_array(strtolower($user->email), config('restricted_apps.allowed_emails', []), true),
            403,
            'このアプリの利用権限がありません。',
        );

        return $next($request);
    }
}
