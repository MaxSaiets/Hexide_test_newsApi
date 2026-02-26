<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PublicNewsController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NewsBlockController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/profile', [ProfileController::class,'show_profile'])->middleware('auth:sanctum');

// post for upload files or put but on the front do post with _method = PUT
Route::put('/profile', [ProfileController::class,'update_profile'])->middleware('auth:sanctum');

Route::get('/news', [PublicNewsController::class, 'index']);
Route::get('/news/{slug}', [PublicNewsController::class, 'get_new_by_slug']);

Route::apiResource('/user_news',NewsController::class)->middleware('auth:sanctum');

Route::apiResource('/new_blocks',NewsBlockController::class)->middleware('auth:sanctum');   //have policies now  ->middleware('checkBlockOwner');