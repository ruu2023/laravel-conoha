<?php

use Illuminate\Support\Facades\Route;

// Universal Draft App (文字数カウント自動保存メモ).
Route::get('/', fn () => view('memo.index'));
