<?php

use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [AuthController::class, 'reset']);

    Route::prefix('user')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [UserController::class, 'user']);
        Route::post('/logout', [UserController::class, 'logout']);
    });
});
