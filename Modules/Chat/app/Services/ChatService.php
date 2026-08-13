<?php

namespace Modules\Chat\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Chat\Events\MessageSent;
use Modules\Chat\Models\Conversation;
use Modules\Chat\Models\Message;

class ChatService
{
    /**
     * Get user's active conversations list ordered by recent messages.
     */
    public function getUserConversations(User $user): Collection
    {
        return Conversation::with(['athlete', 'trainer', 'latestMessage.sender'])
            ->where('athlete_id', $user->id)
            ->orWhere('trainer_id', $user->id)
            ->orderBy('last_message_at', 'desc')
            ->get();
    }

    /**
     * Get available contactable users for quick conversation start.
     */
    public function getAvailableContacts(User $user): Collection
    {
        if ($user->hasRole('admin')) {
            return User::where('id', '!=', $user->id)->take(20)->get();
        }

        if ($user->hasRole('trainer')) {
            $athleteIds = \Modules\Subscription\Models\Booking::where('trainer_id', $user->id)->pluck('user_id');
            return User::whereIn('id', $athleteIds)
                ->orWhereHas('roles', fn($q) => $q->where('name', 'admin'))
                ->get();
        }

        // For Members: Strictly Assigned Trainers (from bookings) + Admin Support Line
        $trainerIds = \Modules\Subscription\Models\Booking::where('user_id', $user->id)->pluck('trainer_id');
        
        return User::whereIn('id', $trainerIds)
            ->orWhereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $user->id)
            ->get();
    }

    /**
     * Load full room data and mark unread messages as read.
     */
    public function loadRoomData(Conversation $conversation, User $currentUser): Conversation
    {
        $conversation->load(['athlete', 'trainer', 'messages.sender']);
        $this->markAsRead($conversation, $currentUser->id);
        return $conversation;
    }

    /**
     * Process optional file attachment, save message, and broadcast event.
     */
    public function processAndSendMessage(Conversation $conversation, User $sender, ?string $messageContent, ?UploadedFile $attachment = null): Message
    {
        $attachmentUrl = null;

        if ($attachment) {
            $path = $attachment->store('chat_attachments', 'public');
            $attachmentUrl = asset('storage/' . $path);
        }

        return $this->sendMessage($conversation, $sender, $messageContent, $attachmentUrl);
    }

    /**
     * Save message record to DB and broadcast real-time WebSockets event.
     */
    public function sendMessage(Conversation $conversation, User $sender, ?string $messageContent, ?string $attachmentUrl = null): Message
    {
        return DB::transaction(function () use ($conversation, $sender, $messageContent, $attachmentUrl) {
            $receiverId = ($sender->id === $conversation->athlete_id) 
                ? ($conversation->trainer_id ?? User::role('admin')->first()?->id ?? $sender->id)
                : $conversation->athlete_id;

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'receiver_id' => $receiverId,
                'message' => $messageContent,
                'attachment_url' => $attachmentUrl,
            ]);

            $conversation->update(['last_message_at' => now()]);

            broadcast(new MessageSent($message))->toOthers();

            return $message;
        });
    }

    /**
     * Locate existing conversation or instantiate new conversation between two users.
     */
    public function findOrCreateBetweenUsers(User $currentUser, User $targetUser): Conversation
    {
        $athleteId = $currentUser->hasRole('trainer') ? $targetUser->id : $currentUser->id;
        $trainerId = $currentUser->hasRole('trainer') ? $currentUser->id : $targetUser->id;
        $type = $targetUser->hasRole('admin') ? 'support' : 'pt_session';

        return $this->getOrCreateConversation($athleteId, $trainerId, $type);
    }

    /**
     * Get or create a conversation between an athlete and a trainer.
     */
    public function getOrCreateConversation(int $athleteId, ?int $trainerId, string $type = 'pt_session'): Conversation
    {
        return DB::transaction(function () use ($athleteId, $trainerId, $type) {
            $query = Conversation::where('athlete_id', $athleteId)
                ->where('type', $type);

            if ($trainerId) {
                $query->where('trainer_id', $trainerId);
            } else {
                $query->whereNull('trainer_id');
            }

            $conversation = $query->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'athlete_id' => $athleteId,
                    'trainer_id' => $trainerId,
                    'type' => $type,
                    'last_message_at' => now(),
                ]);
            }

            return $conversation;
        });
    }

    /**
     * Mark all unread messages in a conversation as read.
     */
    public function markAsRead(Conversation $conversation, int $userId): void
    {
        $conversation->messages()
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get total unread messages count for a user across all conversations.
     */
    public function getTotalUnreadCount(int $userId): int
    {
        return Message::where('receiver_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
