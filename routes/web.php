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

Route::get('lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'uk'])) {
        abort(404);
    }
    session()->put('locale', $locale);
    return redirect()->back();
})->name('lang');