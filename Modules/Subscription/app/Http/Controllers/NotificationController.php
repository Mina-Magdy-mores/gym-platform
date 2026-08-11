<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Subscription\Services\NotificationService;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display paginated notifications index view for Web.
     */
    public function index(Request $request): View
    {
        $notifications = $this->notificationService->getUserNotifications($request->user());

        return view('subscription::notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read and return back.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $this->notificationService->markAsRead($request->user(), $id);

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all user notifications as read and return back.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $this->notificationService->markAllAsRead($request->user());

        return back()->with('success', 'All notifications marked as read.');
    }
}
