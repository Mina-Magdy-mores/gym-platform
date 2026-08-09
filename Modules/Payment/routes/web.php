<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\AdminPaymentController;
use Modules\Payment\Http\Controllers\InvoiceController;
use Modules\Payment\Http\Controllers\PaymentController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('payments', PaymentController::class)->names('payment');
    Route::get('invoices/{payment}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{payment}/download', [InvoiceController::class, 'download'])->name('invoices.download');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');
});
