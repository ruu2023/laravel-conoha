<?php

use Illuminate\Support\Facades\Route;

// Text Stats App (リアルタイム文章統計).
Route::get('/', fn () => view('textstats.index'));
