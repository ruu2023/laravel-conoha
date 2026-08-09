<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Disabled mini apps
    |--------------------------------------------------------------------------
    |
    | App names (routes/apps/{name}.php) to take offline. This file is
    | version-controlled, so the normal way to disable an app is to add its
    | name here, commit, and push — the existing deploy pipeline (GitHub
    | Actions -> ConoHa WING) handles the rest, no SSH needed.
    |
    | See routes/web.php (which globs routes/apps/*.php and subtracts this
    | list) and docs/subdomain-routing.md. (There's also an env-based
    | DISABLED_APPS switch in config/app.php for an emergency SSH-only
    | toggle when you can't wait for a deploy.)
    |
    */

    "disabled" => ["post"],
];
