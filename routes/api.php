<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    dd('test API update');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/logout', [AuthenticationController::class, 'logout']);
    Route::get('/me', [AuthenticationController::class, 'me']);
    Route::post('/post  ', [PostController::class, 'store']);
    Route::patch('/post/{id}', [PostController::class, 'update'])->middleware('pemilik-postingan');
    Route::delete('/post/{id}', [PostController::class, 'destroy'])->middleware('pemilik-postingan');

    Route::post('/comment', [CommentController::class, 'store']);
    Route::patch('/comment/{id}', [CommentController::class, 'update'])->middleware('pemilik-komentar');
    Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->middleware('pemilik-komentar');
});

Route::get('/posts/{username}', [PostController::class, 'showByUser']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/post/{id}', [PostController::class, 'show']);

Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/login', [AuthenticationController::class, 'login']);

Route::get('image/{filename}', [ImageController::class, 'getImage']);
