<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\Api\ApiChatController;

/*
|--------------------------------------------------------------------------
| API Routes for Real-Time Chat Module (v1)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('v1/chats')->group(function () {

    // Conversations List & Contact Book
    Route::get('/', [ApiChatController::class, 'index']);

    // Initialize or Find 1-on-1 Conversation
    Route::post('/start', [ApiChatController::class, 'start']);

    // Conversation Room Messages History
    Route::get('/{conversation}', [ApiChatController::class, 'show']);

    // Send Message with WebSockets Real-time Broadcast (Rate Limited: 30 msgs/min)
    Route::post('/{conversation}/messages', [ApiChatController::class, 'sendMessage'])->middleware('throttle:chat');

});
