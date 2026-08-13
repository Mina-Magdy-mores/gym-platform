<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ChatController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chats', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chats/start/{targetUser}', [ChatController::class, 'startWithUser'])->name('chat.start');
    Route::get('/chats/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chats/{conversation}/messages', [ChatController::class, 'store'])->name('chat.store');
});
