<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Payment\Services\PaymentService;
use Modules\Subscription\Http\Requests\BookSessionRequest;
use Modules\Subscription\Http\Requests\SubscribePlanRequest;
use Modules\Subscription\Services\BookingService;
use Modules\Subscription\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;
    protected BookingService $bookingService;
    protected PaymentService $paymentService;

    public function __construct(
        SubscriptionService $subscriptionService,
        BookingService $bookingService,
        PaymentService $paymentService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->bookingService = $bookingService;
        $this->paymentService = $paymentService;
    }

    /**
     * Display member dashboard cleanly inside Subscription Module.
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $activeSub = $user->activeSubscription;
        $gymRules = $this->subscriptionService->getActiveGymRules();
        $upcomingBookings = $this->bookingService->getUserBookings($user);

        return view('dashboard', compact('user', 'activeSub', 'gymRules', 'upcomingBookings'));
    }

    /**
     * Display subscription plans and gym operating schedules with Paymob callback handling.
     */
    public function plans(Request $request): View
    {
        if ($request->query('success') === 'true' && ! session()->has('status')) {
            session()->flash('status', 'subscribed');
        }

        $plans = $this->subscriptionService->getActivePlans();
        $menSchedules = $this->subscriptionService->getGymSchedules('men');
        $womenSchedules = $this->subscriptionService->getGymSchedules('women');

        return view('subscription::plans', compact('plans', 'menSchedules', 'womenSchedules'));
    }

    /**
     * Display plan checkout, action preview (Upgrade / Queued), and terms review page cleanly with exception handling.
     */
    public function checkout(Request $request, int $planId)
    {
        try {
            $plan = $this->subscriptionService->getPlanById($planId);
            $prep = $this->subscriptionService->prepareSubscriptionAction($request->user(), $plan);
            $gymRules = $this->subscriptionService->getActiveGymRules();

            return view('checkout', compact('plan', 'prep', 'gymRules'));
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Subscribe user to a plan via payment checkout process with exception handling.
     */
    public function subscribe(SubscribePlanRequest $request): RedirectResponse
    {
        try {
            $plan = $this->subscriptionService->getPlanById(
                $request->validated('subscription_plan_id')
            );

            $paymentResponse = $this->paymentService->processSubscriptionPayment(
                $request->user(),
                $plan
            );

            if ($paymentResponse->redirectUrl) {
                return redirect()->away($paymentResponse->redirectUrl);
            }

            return redirect()->route('dashboard')->with('status', 'subscribed');
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Display trainer bookings page via service layer.
     */
    public function bookings(Request $request): View
    {
        $trainers = $this->bookingService->getAllTrainers();
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
