<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $userId,
        public readonly string $title,
        public readonly string $message,
        public readonly string $type,
        public readonly string $createdAt,
    ) {}

    /**
     * Broadcast on the recipient's private personal channel.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    /**
     * Event name the JS listener will use.
     */
    public function broadcastAs(): string
    {
        return 'notification.sent';
    }

    /**
     * Data payload sent to the frontend.
     */
    public function broadcastWith(): array
    {
        return [
            'title'      => $this->title,
            'message'    => $this->message,
            'type'       => $this->type,
            'created_at' => $this->createdAt,
        ];
    }
}
