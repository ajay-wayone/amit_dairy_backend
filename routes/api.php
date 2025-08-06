<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('signup', [AuthController::class, 'signup']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    });
});
