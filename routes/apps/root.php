<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Apex domain (ruu-dev.com). Formerly a standalone Next.js static export
// (resources/ruu-dev, removed) — ported to Inertia+React in this same
// Laravel app. See ResolveAppSubdomain::resolve() for why this app is
// matched differently from every other subdomain.

Route::get('/', fn () => Inertia::render('Root/Welcome'));

Route::get('/dashboard', fn () => Inertia::render('Root/Dashboard', [
    'posts' => json_decode(file_get_contents(resource_path('data/posts.json')), associative: true),
]));
