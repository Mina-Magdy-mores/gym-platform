<?php

namespace Modules\Subscription\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Modules\Subscription\Adapters\MockSmsAdapter;
use Modules\Subscription\Console\Commands\ActivateQueuedSubscriptionsCommand;
use Modules\Subscription\Console\Commands\SendBookingRemindersCommand;
use Modules\Subscription\Console\Commands\CheckExpiringSubscriptionsCommand;
use Modules\Subscription\Contracts\SmsGatewayInterface;

class SubscriptionServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Subscription';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'subscription';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        ActivateQueuedSubscriptionsCommand::class,
        SendBookingRemindersCommand::class,
        CheckExpiringSubscriptionsCommand::class,
    ];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Register module services and interface bindings.
     */
    public function register(): void
    {
        // Bind SMS Gateway Interface to active MockSmsAdapter for development environment
        $this->app->bind(
            SmsGatewayInterface::class,
            MockSmsAdapter::class
        );
    }
}
