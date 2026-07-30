<?php

use Illuminate\Support\Facades\Route;

// Legacy top-level host (laravel.ruu-dev.com) and the no-header fallback.
// memo.ruu-dev.com is canonical now, so redirect instead of duplicating content.
Route::get('/', fn () => redirect('https://memo.ruu-dev.com/', 301));
