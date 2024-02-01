<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewController;
use App\Http\Controllers\SubcommentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/users', [UserController::class, 'store']);

Route::get('/categories', [CategorieController::class, 'index']);

Route::get('/news', [NewController::class, 'index']);
Route::get('/news/{id}', [NewController::class, 'show']);

Route::get('/comment', [CommentController::class, 'index']);
Route::get('/comment/{id}', [CommentController::class, 'show']);

Route::get('/subcomment', [SubcommentController::class, 'index']);
Route::get('/subcomment/{id}', [SubcommentController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // Users
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Categories
    Route::post('/categories', [CategorieController::class, 'store']);
    Route::put('/categories/{id}', [CategorieController::class, 'update']);
    Route::get('/categories/{id}', [CategorieController::class, 'show']);
    Route::delete('/categories/{id}', [CategorieController::class, 'destroy']);

    // News
    Route::post('/news', [NewController::class, 'store']);
    Route::put('/news/{id}', [NewController::class, 'update']);
    Route::delete('/news/{id}', [NewController::class, 'destroy']);

    // Comments
    Route::post('/comment', [CommentController::class, 'store']);
    Route::put('/comment/{id}', [CommentController::class, 'update']);
    Route::post('/comment/{id}/like', [CommentController::class, 'like']);
    Route::delete('/comment/{id}', [CommentController::class, 'destroy']);

    // Sub-Comments
    Route::post('/subcomment', [SubcommentController::class, 'store']);
    Route::put('/subcomment/{id}', [SubcommentController::class, 'update']);
    Route::post('/subcomment/{id}/like', [SubcommentController::class, 'like']);
    Route::delete('/subcomment/{id}', [SubcommentController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
