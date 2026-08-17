<?php

namespace Modules\Chat\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Chat\Http\Requests\SendMessageRequest;
use Modules\Chat\Models\Conversation;
use Modules\Chat\Services\ChatService;

class ApiChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Get active chat conversations & available contacts for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = $this->chatService->getUserConversations($user);
        $availableContacts = $this->chatService->getAvailableContacts($user);

        return response()->json([
            'success' => true,
            'data' => [
                'conversations' => $conversations,
                'available_contacts' => $availableContacts,
            ],
        ]);
    }

    /**
     * Get room messages history and participants.
     */
    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if ($conversation->athlete_id !== $user->id && $conversation->trainer_id !== $user->id && ! $user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to view this conversation.'], 403);
        }

        $conversationData = $this->chatService->loadRoomData($conversation, $user);

        return response()->json([
            'success' => true,
            'data' => $conversationData,
        ]);
    }

    /**
     * Send real-time chat message with optional image attachment via WebSockets.
     */
    public function sendMessage(SendMessageRequest $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        if ($conversation->athlete_id !== $user->id && $conversation->trainer_id !== $user->id && ! $user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to post messages in this conversation.'], 403);
        }

        $message = $this->chatService->processAndSendMessage(
            $conversation,
            $user,
            $request->validated('message'),
            $request->file('attachment')
        );

        return response()->json([
            'success' => true,
            'message' => 'Message sent and broadcasted successfully.',
            'data' => $message->load('sender:id,name,email'),
        ], 201);
    }

    /**
     * Find or create 1-on-1 conversation with a target user (trainer/support).
     */
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = $request->user();
        $targetUser = User::findOrFail($request->input('user_id'));

        if ($user->id === $targetUser->id) {
            return response()->json(['success' => false, 'message' => 'Cannot create chat room with yourself.'], 422);
        }

        $conversation = $this->chatService->findOrCreateBetweenUsers($user, $targetUser);

        return response()->json([
            'success' => true,
            'message' => 'Chat room initialized successfully.',
            'data' => $conversation->load(['athlete:id,name', 'trainer:id,name']),
        ]);
    }
}
