<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('mobile')->group(function () {
    // Authentication routes for mobile
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login/google', [AuthController::class, 'loginWithGoogle']);
    Route::post('/password/email', [AuthController::class, 'forgotPassword']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

