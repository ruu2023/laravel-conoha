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
 */
class EnsureAllowedGoogleUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        abort_unless(
            $user && in_array(strtolower($user->email), config('restricted_apps.allowed_emails', []), true),
            403,
            'このアプリの利用権限がありません。',
        );

        return $next($request);
    }
}
