<?php

use Illuminate\Support\Facades\Broadcast;
use Modules\Chat\Models\Conversation;

/*
|--------------------------------------------------------------------------
| Broadcast Channels Authorization
|--------------------------------------------------------------------------
*/

// 1. Private Chat Room Channel
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    return (int) $user->id === (int) $conversation->athlete_id ||
           (int) $user->id === (int) $conversation->trainer_id ||
           $user->hasRole('admin');
});

// 2. Personal User Notifications & Chat Toasts Channel
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// 3. Community Online Presence Channel
Broadcast::channel('gym-community', function ($user) {
    if (!$user) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->getRoleNames()->first() ?? 'member',
        'avatar' => $user->hasMedia('avatar') ? $user->getFirstMediaUrl('avatar', 'thumb') : null,
    ];
});
