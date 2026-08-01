<?php

use App\Http\Middleware\EnsureAllowedGoogleUser;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(EnsureAllowedGoogleUser::class)
    ->get('/', fn () => Inertia::render('Zundamon/Index'))
    ->name('zundamon.home');
