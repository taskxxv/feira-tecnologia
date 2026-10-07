<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// O relato é anônimo e, por isso, não exige autenticação.
Route::post('/reports', [ReportController::class, 'store']);

Route::get('/subjects', fn () => \App\Models\Subject::orderBy('name')->get(['id', 'name']));

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/my/posts', [PostController::class, 'mine']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::post('/posts/{post}/comments', [PostController::class, 'comment']);
    Route::post('/chat', ChatController::class);

    Route::middleware('admin')->group(function (): void {
        Route::patch('/posts/{post}/moderate', [PostController::class, 'moderate']);
        Route::get('/reports', [ReportController::class, 'index']);
        Route::patch('/reports/{report}', [ReportController::class, 'update']);
    });
});
