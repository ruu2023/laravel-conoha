<?php

use Illuminate\Support\Facades\Route;

// 画像切り抜きツール(ブラウザ内で完結、アップロードなし).
Route::get('/', fn () => view('imagecrop.index'));
