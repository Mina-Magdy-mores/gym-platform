<?php

use Illuminate\Support\Facades\Route;
use Modules\Wallet\Http\Controllers\AdminPayoutController;
use Modules\Wallet\Http\Controllers\WalletController;

/*
|--------------------------------------------------------------------------
| Web Routes for Wallet Module
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:trainer|admin'])->group(function () {
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('wallet/payout', [WalletController::class, 'store'])->name('wallet.payout.store');
    Route::get('payouts/{payoutRequest}/voucher', [WalletController::class, 'voucher'])->name('payouts.voucher');
    Route::get('payouts/{payoutRequest}/download', [WalletController::class, 'downloadVoucher'])->name('payouts.download');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('payouts', [AdminPayoutController::class, 'index'])->name('admin.payouts.index');
    Route::post('payouts/{payoutRequest}/approve', [AdminPayoutController::class, 'approve'])->name('admin.payouts.approve');
    Route::post('payouts/{payoutRequest}/reject', [AdminPayoutController::class, 'reject'])->name('admin.payouts.reject');
});
