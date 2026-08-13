<?php

namespace Modules\Chat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\Models\Message;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load(['sender', 'conversation']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            // 1. القناة المغلقة الخاصة بالغرفة نفسها
            new PrivateChannel('chat.' . $this->message->conversation_id),
            
            // 2. القناة المغلقة الخاصة بالطرف المستقبل لإشعارات الشات عبر أي صفحة
            new PrivateChannel('user.' . $this->message->receiver_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $sender = $this->message->sender;
        $avatarUrl = $sender && $sender->hasMedia('avatar') 
            ? $sender->getFirstMediaUrl('avatar', 'thumb') 
            : null;

        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $sender->name ?? 'User',
            'sender_avatar' => $avatarUrl,
            'receiver_id' => $this->message->receiver_id,
            'message' => $this->message->message,
            'attachment_url' => $this->message->attachment_url,
            'created_at' => $this->message->created_at->format('h:i A'),
            'created_at_human' => $this->message->created_at->diffForHumans(),
        ];
    }
}