<?php

use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// api/v1
Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers\Api\V1'], function () {
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('invoices', InvoiceController::class);

    // Authentication
    Route::post('register', [AuthController::class, 'register'])->middleware('setLocale');
    Route::post('login', [AuthController::class, 'login'])->middleware('setLocale');
    Route::get('logout', [AuthController::class, 'logout'])->middleware(['auth.jwt', 'setLocale']);
    Route::post('change-password', [AuthController::class, 'changePassword'])->middleware(['auth.jwt'])->middleware('setLocale');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('setLocale');
    Route::post('send-verification-email', [AuthController::class, 'sendVerificationEmail'])->middleware('setLocale');
    Route::get('verify-email', [AuthController::class, 'verifyEmail'])->middleware('setLocale');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('setLocale');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('setLocale');
});
