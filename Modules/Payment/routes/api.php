<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\Api\ApiAdminPaymentController;
use Modules\Payment\Http\Controllers\Api\ApiInvoiceController;
use Modules\Payment\Http\Controllers\Api\ApiPaymentController;
use Modules\Payment\Http\Controllers\PaymentController;

// Public Payment Gateway Webhook Callback Route (No Auth Middleware)
Route::post('v1/payments/webhook', [ApiPaymentController::class, 'handleWebhook'])->name('payment.webhook');

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payments', PaymentController::class)->names('payment');
    Route::get('invoices/{payment}', [ApiInvoiceController::class, 'show'])->name('api.v1.invoices.show');
    Route::get('invoices/{payment}/download', [ApiInvoiceController::class, 'download'])->name('api.v1.invoices.download');

    // Admin Financial Ledger API
    Route::get('admin/payments', [ApiAdminPaymentController::class, 'index'])->name('api.v1.admin.payments.index');
});