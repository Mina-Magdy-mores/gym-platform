<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Subscription\Http\Requests\BookSessionRequest;
use Modules\Subscription\Http\Requests\SubscribePlanRequest;
use Modules\Subscription\Services\BookingService;
use Modules\Subscription\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;
    protected BookingService $bookingService;

    public function __construct(SubscriptionService $subscriptionService, BookingService $bookingService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->bookingService = $bookingService;
    }

    /**
     * Display subscription plans and gym operating schedules.
     */
    public function plans(): View
    {
        $plans = $this->subscriptionService->getActivePlans();
        $menSchedules = $this->subscriptionService->getGymSchedules('men');
        $womenSchedules = $this->subscriptionService->getGymSchedules('women');

        return view('subscription::plans', compact('plans', 'menSchedules', 'womenSchedules'));
    }

    /**
     * Subscribe user to a plan via Web form.
     */
    public function subscribe(SubscribePlanRequest $request): RedirectResponse
    {
        $this->subscriptionService->subscribeUser(
            $request->user(),
            $request->validated('subscription_plan_id')
        );

        return redirect()->route('plans.index')->with('status', 'subscribed');
    }

    /**
     * Display trainer bookings page.
     */
    public function bookings(Request $request): View
    {
        $trainers = User::role('trainer')->get();
        $bookings = $this->bookingService->getUserBookings($request->user());

        return view('subscription::bookings', compact('trainers', 'bookings'));
    }

    /**
     * Book a private trainer session via Web form.
     */
    public function bookSession(BookSessionRequest $request): RedirectResponse
    {
        try {
            $this->bookingService->bookTrainerSession(
                $request->user(),
                $request->validated()
            );

            return redirect()->route('bookings.index')->with('status', 'booked');
        } catch (\Exception $e) {
            return redirect()->route('bookings.index')->with('error', $e->getMessage());
        }
    }
}
