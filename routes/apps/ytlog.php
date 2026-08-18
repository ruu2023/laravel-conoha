<?php

use Illuminate\Support\Facades\Route;

// YT_LOG.exe (YouTube動画のタイムスタンプ付きメモ).
Route::get('/', fn () => view('ytlog.index'));
