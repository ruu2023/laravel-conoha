<?php

use Illuminate\Support\Facades\Route;

// MyBatis 30分マスター講義 (ステップ学習 + 進捗localStorage保存).
Route::get('/', fn () => view('mybatistutor.index'));
