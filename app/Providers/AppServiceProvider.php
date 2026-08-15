<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Events\NotificationSent as RealtimeNotificationSent;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ─── Real-Time WebSocket Notification Dispatcher ───
        // Whenever any notification is saved to the 'database' channel,
        // automatically broadcast it in real-time to the recipient's private channel.
        Event::listen(NotificationSent::class, function (NotificationSent $event) {
            if ($event->channel === 'database' && isset($event->notifiable->id)) {
                $data = method_exists($event->notification, 'toArray')
                    ? $event->notification->toArray($event->notifiable)
                    : [];

                broadcast(new RealtimeNotificationSent(
                    userId:    (int) $event->notifiable->id,
                    title:     $data['title']   ?? 'New Notification',
                    message:   $data['message'] ?? '',
                    type:      $data['type']    ?? 'general',
                    createdAt: now()->toIso8601String(),
                ));
            }
        });
    }
}
