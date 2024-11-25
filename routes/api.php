<?php

use App\Http\Controllers\Backend\ArticleManageController;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [AuthController::class, 'reset']);

    Route::get('/articles', [ArticleManageController::class, 'index']);
    Route::get('/articles/{slug}', [ArticleManageController::class, 'show']);

    Route::prefix('user')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [UserController::class, 'user']);
        Route::get('/preferences', [UserPreferenceController::class, 'show']);
        Route::post('/preferences', [UserPreferenceController::class, 'store']);
        Route::get('/articles', [ArticleManageController::class, 'personalizedFeed']);
        Route::post('/logout', [UserController::class, 'logout']);
    });
});
