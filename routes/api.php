<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TransactionController;
use App\Factories\PaymentProviderFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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


Route::post('/webhook/payment', function (Request $request): JsonResponse {
    try {
        $providerKey = config('payment.default_provider');
        $provider = PaymentProviderFactory::make($providerKey);

        $payload = $request->all();

        $result = $provider->handleWebhook($payload);

        return response()->json(['status' => 'ok', 'data' => $result]);
    } catch (\Throwable $e) {
        Log::error('Stripe webhook failed: ' . $e->getMessage(), [
            'payload' => $request->all()
        ]);

        return response()->json(['error' => 'Webhook failed: ' . $e->getMessage()], 400);
    }
})->middleware([SecureAPI::class, 'throttle:10,1']);