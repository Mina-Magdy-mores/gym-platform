<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Modules\Subscription\Services\SubscriptionService;

class WelcomeController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display the welcome landing page with active subscription plans and schedules.
     */
    public function index(): View
    {
        $plans = $this->subscriptionService->getActivePlans();
        $menSchedules = $this->subscriptionService->getGymSchedules('men');
        $womenSchedules = $this->subscriptionService->getGymSchedules('women');

        return view('welcome', compact('plans', 'menSchedules', 'womenSchedules'));
    }
}
