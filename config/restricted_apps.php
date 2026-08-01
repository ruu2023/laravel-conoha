<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google-login-gated mini apps
    |--------------------------------------------------------------------------
    |
    | techpulse and zundamon are gray-area on copyright, so access is limited
    | to a hand-picked allow-list of Google accounts. Login itself only
    | happens on the "laravel" app (routes/apps/laravel.php — the no-header /
    | bare-host fallback, repurposed as the shared login hub so only one
    | Google OAuth redirect URI needs registering instead of one per gated
    | subdomain). Listed together here — not one config block per app —
    | because all three intentionally share one session: see
    | App\Http\Middleware\ResolveAppSubdomain, which reads this same list to
    | widen the session cookie's domain only for these subdomains.
    */
    'session_shared_apps' => ['laravel', 'techpulse', 'zundamon'],

    /*
    | Comma-separated in .env, never committed here — this repo is public,
    | and allow-listed people's email addresses are not something to publish.
    | Update via SSH + `.env` on the server, no deploy needed.
    */
    'allowed_emails' => array_filter(array_map(
        fn ($email) => strtolower(trim($email)),
        explode(',', (string) env('RESTRICTED_APPS_ALLOWED_EMAILS', '')),
    )),
];
