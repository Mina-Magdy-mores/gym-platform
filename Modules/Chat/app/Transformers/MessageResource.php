<?php

namespace Modules\Chat\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $sender = $this->sender;
        $avatar = $sender && $sender->hasMedia('avatar') 
            ? $sender->getFirstMediaUrl('avatar', 'thumb') 
            : null;

        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->sender_id,
            'sender_name' => $sender->name ?? 'User',
            'sender_avatar' => $avatar,
            'message' => $this->message,
            'attachment_url' => $this->attachment_url,
            'read_at' => $this->read_at?->toIso8601String(),
            'created_at' => $this->created_at->format('h:i A'),
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }
}
