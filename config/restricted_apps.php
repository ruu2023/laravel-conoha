<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google-login-gated mini apps
    |--------------------------------------------------------------------------
    |
    | techpulse and zundamon are gray-area on copyright, so access is limited
    | to a hand-picked allow-list of Google accounts. Login itself only
    | happens on the "login" app (routes/apps/login.php), the shared login
    | hub, so only one Google OAuth redirect URI needs registering instead
    | of one per gated app. All apps share a single session cookie (same
    | domain, path-based routing), so no separate session-sharing config is
    | needed for login/techpulse/zundamon to see the same login.
    */

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
