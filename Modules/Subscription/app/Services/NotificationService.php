<?php

namespace Modules\Subscription\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;

class NotificationService
{
    /**
     * Get user notifications list paginated.
     */
    public function getUserNotifications(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->notifications()->latest()->paginate($perPage);
    }

    /**
     * Get unread notifications count for user.
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Mark single notification as read for user.
     */
    public function markAsRead(User $user, string $notificationId): DatabaseNotification
    {
        /** @var DatabaseNotification $notification */
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return $notification;
    }

    /**
     * Mark all unread notifications as read for user.
     */
    public function markAllAsRead(User $user): int
    {
        $unreadCount = $user->unreadNotifications()->count();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return $unreadCount;
    }
}
