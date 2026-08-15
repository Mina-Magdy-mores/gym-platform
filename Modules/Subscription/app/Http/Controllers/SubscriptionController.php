<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Payment\Services\PaymentService;
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
        if ($activeSub) {
            $activeSub->load(['plan', 'payment']);
        }
        $gymRules = $this->subscriptionService->getActiveGymRules();
        $upcomingBookings = $this->bookingService->getUserBookings($user);
        $memberPayments = $user->payments()->with(['plan', 'booking.trainer'])->latest()->paginate(10);

        $trainerStats = null;
        $adminStats = null;
        $adminRecentBookings = collect();
        $adminRecentPayments = collect();
        $assignedAthletes = collect();

        if ($user->hasRole('admin')) {
            $totalRevenue = (float) \Modules\Payment\Models\Payment::whereIn('status', ['completed', 'successful', 'paid'])
                ->where('amount', '>', 0)
                ->sum('amount');
            $activeMembersCount = \Modules\Subscription\Models\UserSubscription::where('status', 'active')
                ->where('ends_at', '>=', now())
                ->distinct('user_id')
                ->count('user_id');
            $totalMembersCount = \App\Models\User::role('member')->count();
            if ($totalMembersCount === 0) {
                $totalMembersCount = \App\Models\User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['admin', 'trainer']))->count();
            }
            $totalTrainersCount = \App\Models\User::role('trainer')->count();
            $todayBookingsCount = \Modules\Subscription\Models\Booking::where('booking_date', date('Y-m-d'))->count();
            $pendingPayoutsCount = \Modules\Wallet\Models\PayoutRequest::where('status', 'pending')->count();
            $pendingPayoutsAmount = (float) \Modules\Wallet\Models\PayoutRequest::where('status', 'pending')->sum('amount');

            $adminStats = [
                'totalRevenue' => $totalRevenue,
                'activeMembers' => $activeMembersCount,
                'totalMembers' => max($totalMembersCount, $activeMembersCount),
                'totalTrainers' => $totalTrainersCount,
                'todayBookings' => $todayBookingsCount,
                'pendingPayoutsCount' => $pendingPayoutsCount,
                'pendingPayoutsAmount' => $pendingPayoutsAmount,
                'unreadMessages' => (new \Modules\Chat\Services\ChatService())->getTotalUnreadCount($user->id),
            ];

            $adminRecentBookings = \Modules\Subscription\Models\Booking::with(['user.activeSubscription.plan', 'trainer'])
                ->latest()
                ->take(6)
                ->get();

            $adminRecentPayments = \Modules\Payment\Models\Payment::with(['user', 'plan'])
                ->whereIn('status', ['completed', 'successful', 'paid'])
                ->where('amount', '>', 0)
                ->latest()
                ->take(6)
                ->get();
        } elseif ($user->hasRole('trainer')) {
            $assignedAthletes = \App\Models\User::role('member')
                ->whereHas('bookings', function($b) use ($user) {
                    $b->where('trainer_id', $user->id);
                })
                ->where('id', '!=', $user->id)
                ->get();

            $trainerStats = [
                'totalAthletes' => $assignedAthletes->count(),
                'todaySessions' => $user->trainerBookings()->where('booking_date', date('Y-m-d'))->where('status', 'confirmed')->count(),
                'totalBookings' => $user->trainerBookings()->count(),
                'walletBalance' => (float) ($user->trainerWallet?->balance ?? 0.00),
                'unreadMessages' => (new \Modules\Chat\Services\ChatService())->getTotalUnreadCount($user->id),
            ];
        }

        return view('dashboard', compact(
            'user',
            'activeSub',
            'gymRules',
            'upcomingBookings',
            'memberPayments',
            'trainerStats',
            'adminStats',
            'adminRecentBookings',
            'adminRecentPayments',
            'assignedAthletes'
        ));
    }

    /**
     * Display subscription plans and gym operating schedules with Paymob callback handling.
     */
    public function plans(Request $request): View
    {
        if ($request->query('success') === 'true' && ! session()->has('status')) {
            session()->flash('status', 'subscribed');
        } elseif ($request->query('success') === 'false' && ! session()->has('error')) {
            $paymobMessage = $request->query('data_message') 
                ?? $request->query('txn_response_code') 
                ?? 'Payment transaction was declined or cancelled. Please try again.';

            session()->flash('error', 'Paymob Gateway Notice: ' . $paymobMessage);
        }

        $plans = $this->subscriptionService->getActivePlans();
        $menSchedules = $this->subscriptionService->getGymSchedules('men');
        $womenSchedules = $this->subscriptionService->getGymSchedules('women');

        return view('subscription::plans', compact('plans', 'menSchedules', 'womenSchedules'));
    }

    /**
     * Display plan checkout, action preview (Upgrade / Queued), and terms review page cleanly with exception handling.
     */
    public function checkout(Request $request, int $planId): View|RedirectResponse
    {
        if ($request->user()->hasAnyRole(['trainer', 'admin', 'super-admin'])) {
            return redirect()->route('plans.index')->with('error', 'Trainer and Staff accounts cannot purchase member subscriptions.');
        }

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
                $plan,
                $request->input('gateway', 'paymob')
            );

            if ($paymentResponse->redirectUrl) {
                return redirect()->away($paymentResponse->redirectUrl);
            }

            return redirect()->route('dashboard')->with('status', 'subscribed');
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', $e->getMessage());
        }
    }
}