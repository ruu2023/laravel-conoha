<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Apex domain (ruu-dev.com). Formerly a standalone Next.js static export
// (resources/ruu-dev, removed) — ported to Inertia+React in this same
// Laravel app. Unlike every other mini app, this one is registered with no
// URI prefix (see routes/web.php), so it serves ruu-dev.com/ directly.

Route::get('/', fn () => Inertia::render('Root/Welcome', [
    'posts' => json_decode(file_get_contents(resource_path('data/posts.json')), associative: true),
]));
