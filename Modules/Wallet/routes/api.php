<?php

use Illuminate\Support\Facades\Route;
use Modules\Wallet\Http\Controllers\Api\ApiAdminPayoutController;
use Modules\Wallet\Http\Controllers\Api\ApiWalletController;

/*
|--------------------------------------------------------------------------
| API Routes for Wallet Module
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Trainer Wallet & Payout Requests
    Route::get('trainer/wallet', [ApiWalletController::class, 'trainerWallet'])->name('api.v1.trainer.wallet');
    Route::post('trainer/wallet/payout', [ApiWalletController::class, 'requestPayout'])->name('api.v1.trainer.wallet.payout');

    // Admin Payout Management
    Route::get('admin/payouts', [ApiAdminPayoutController::class, 'index'])->name('api.v1.admin.payouts.index');
    Route::post('admin/payouts/{payoutRequest}/approve', [ApiAdminPayoutController::class, 'approve'])->name('api.v1.admin.payouts.approve');
    Route::post('admin/payouts/{payoutRequest}/reject', [ApiAdminPayoutController::class, 'reject'])->name('api.v1.admin.payouts.reject');
});
