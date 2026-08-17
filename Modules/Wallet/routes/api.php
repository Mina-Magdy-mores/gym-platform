<?php

use Illuminate\Support\Facades\Route;
use Modules\Wallet\Http\Controllers\Api\ApiAdminPayoutController;
use Modules\Wallet\Http\Controllers\Api\ApiWalletController;

/*
|--------------------------------------------------------------------------
| API Routes for Wallet Module (v1)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Trainer Wallet, Payout Requests & Vouchers
    Route::get('trainer/wallet', [ApiWalletController::class, 'trainerWallet'])->name('api.v1.trainer.wallet');
    Route::post('trainer/wallet/payout', [ApiWalletController::class, 'requestPayout'])->name('api.v1.trainer.wallet.payout');
    Route::get('payouts/{id}/voucher', [ApiWalletController::class, 'voucher'])->name('api.v1.payouts.voucher');

    // Admin Payout Management
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('payouts', [ApiAdminPayoutController::class, 'index'])->name('api.v1.admin.payouts.index');
        Route::post('payouts/{payoutRequest}/approve', [ApiAdminPayoutController::class, 'approve'])->name('api.v1.admin.payouts.approve');
        Route::post('payouts/{payoutRequest}/reject', [ApiAdminPayoutController::class, 'reject'])->name('api.v1.admin.payouts.reject');
    });
});
