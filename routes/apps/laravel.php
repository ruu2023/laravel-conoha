<?php

use Illuminate\Support\Facades\Route;

// Legacy top-level host (laravel.ruu-dev.com) and the no-header fallback.
// Serves the same content as the memo app during the migration period.
Route::get('/', fn () => view('welcome'));
