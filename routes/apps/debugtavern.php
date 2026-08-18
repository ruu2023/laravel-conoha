<?php

use Illuminate\Support\Facades\Route;

// Debugging Tavern (RPG風エンジニアクイズゲーム).
Route::get('/', fn () => view('debugtavern.index'));
