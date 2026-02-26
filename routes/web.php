<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\WebNewsController;
use App\Http\Controllers\WebProfileController;
use App\Http\Controllers\WebDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/news', [WebNewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [WebNewsController::class, 'show'])->name('news.show');
