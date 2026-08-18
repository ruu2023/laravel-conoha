<?php

use Illuminate\Support\Facades\Route;

// Gravity Dash (Canvas重力回避ゲーム).
Route::get('/', fn () => view('gravitydash.index'));
