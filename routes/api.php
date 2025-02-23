<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Middleware\SecureAPI;


Route::get('/test', function () {
    return 'Hello World';
})->middleware('throttle:3,1');

Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('throttle:10,1');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum','throttle:10,1');

Route::middleware('auth:sanctum', 'throttle:60,1')->group(function () {
    Route::get('/orders', [OrderController::class, 'list']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    Route::post('/orders/{order}/pay', [TransactionController::class, 'pay']);

    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
});


Route::post('/webhook/payment', [PaymentWebhookController::class, 'handle'])->middleware(SecureAPI::class, 'throttle:10,1');