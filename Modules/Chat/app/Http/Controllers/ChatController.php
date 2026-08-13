<?php

namespace Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\Chat\Http\Requests\SendMessageRequest;
use Modules\Chat\Models\Conversation;
use Modules\Chat\Services\ChatService;
use Modules\Chat\Transformers\MessageResource;

class ChatController extends Controller
{
    use AuthorizesRequests;

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Display list of active chat conversations for the logged-in user.
     */
    public function index(): View
    {
        $conversations = $this->chatService->getUserConversations(auth()->user());
        $availableContacts = $this->chatService->getAvailableContacts(auth()->user());
        return view('chat::index', compact('conversations', 'availableContacts'));
    }

    /**
     * Display a specific chat room conversation.
     */
    public function show(Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        $conversationData = $this->chatService->loadRoomData($conversation, auth()->user());
        return view('chat::show', compact('conversationData'));
    }

    /**
     * Store and broadcast a new chat message via WebSockets.
     */
    public function store(SendMessageRequest $request, Conversation $conversation): JsonResponse
    {
        $message = $this->chatService->processAndSendMessage(
            $conversation,
            auth()->user(),
            $request->validated('message'),
            $request->file('attachment')
        );

        return response()->json([
            'success' => true,
            'message' => new MessageResource($message),
        ]);
    }

    /**
     * Start or locate a direct chat conversation with a specific user.
     */
    public function startWithUser(User $targetUser)
    {
        $conversation = $this->chatService->findOrCreateBetweenUsers(auth()->user(), $targetUser);
        return redirect()->route('chat.show', $conversation->id);
    }
}