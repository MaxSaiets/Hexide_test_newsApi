<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\NewsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'uk'])) {
        abort(404);
    }
    session()->put('locale', $locale);
    return redirect()->back();
})->name('lang');