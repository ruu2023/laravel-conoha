<?php

use Illuminate\Support\Facades\Route;

// Quick Markdown (ライブプレビュー付きMarkdownメモ、自動保存).
Route::get('/', fn () => view('quickmd.index'));
