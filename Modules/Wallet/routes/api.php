<?php

use Illuminate\Support\Facades\Route;
use Modules\Wallet\Http\Controllers\Api\ApiWalletController;

/*
|--------------------------------------------------------------------------
| API Routes for Wallet Module
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('trainer/wallet', [ApiWalletController::class, 'trainerWallet'])->name('api.v1.trainer.wallet');
});
