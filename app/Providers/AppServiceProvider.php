<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Events\NotificationSent as RealtimeNotificationSent;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ─── 1. Enterprise Security Rate Limiters ───
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email') . $request->ip());
        });

        RateLimiter::for('chat', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // ─── 2. Real-Time WebSocket Notification Dispatcher ───
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
