<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\Api\ApiPaymentController;
use Modules\Payment\Http\Controllers\PaymentController;

// Public Payment Gateway Webhook Callback Route (No Auth Middleware)
Route::post('v1/payments/webhook', [ApiPaymentController::class, 'handleWebhook'])->name('payment.webhook');

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payments', PaymentController::class)->names('payment');
});