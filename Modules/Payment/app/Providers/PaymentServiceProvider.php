<?php

namespace Modules\Payment\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\Adapters\PaymobAdapter;
use Modules\Payment\Adapters\StripeAdapter;
use Modules\Payment\Adapters\MockPaymentAdapter;

class PaymentServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Payment';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'payment';

    /**
     * Register services and contract bindings.
     */
    public function register(): void
    {
        parent::register();

        $this->app->bind(PaymentGatewayInterface::class, function ($app) {
            $gateway = config('services.payment.default_gateway', env('PAYMENT_GATEWAY', 'paymob'));

            return match (strtolower($gateway)) {
                'stripe' => new StripeAdapter(),
                'mock' => new MockPaymentAdapter(),
                default => new PaymobAdapter(),
            };
        });
    }

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}