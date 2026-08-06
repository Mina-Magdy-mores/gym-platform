<?php

use Illuminate\Support\Facades\Route;
use Modules\Wallet\Http\Controllers\WalletController;

/*
|--------------------------------------------------------------------------
| Web Routes for Wallet Module
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:trainer|admin'])->group(function () {
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
});
