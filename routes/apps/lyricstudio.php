<?php

use Illuminate\Support\Facades\Route;

// Lyric Studio (歌詞メモ + iTunes Search APIでジャケット/曲情報を紐付け).
Route::get('/', fn () => view('lyricstudio.index'));
