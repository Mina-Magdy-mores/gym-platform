<?php

namespace Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Subscription\Services\NotificationService;
use Modules\Subscription\Transformers\NotificationResource;
use Modules\User\Traits\ApiResponseTrait;

class ApiNotificationController extends Controller
{
    use ApiResponseTrait;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get paginated list of notifications for mobile app via NotificationResource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = $this->notificationService->getUserNotifications($user);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        $data = [
            'unread_count' => $unreadCount,
            'notifications' => NotificationResource::collection($notifications)->response()->getData(true),
        ];

        return $this->successResponse($data, 'Notifications fetched successfully.');
    }

    /**
     * Mark a single notification as read via API.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $this->notificationService->markAsRead($request->user(), $id);

        $data = [
            'notification_id' => $id,
            'is_read' => true,
        ];

        return $this->successResponse($data, 'Notification marked as read successfully.');
    }

    /**
     * Mark all user notifications as read via API.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $this->notificationService->markAllAsRead($request->user());

        $data = [
            'unread_count' => 0,
        ];

        return $this->successResponse($data, 'All notifications marked as read successfully.');
    }
}
