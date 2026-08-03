<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Subscription\Http\Requests\Admin\StoreSubscriptionPlanRequest;
use Modules\Subscription\Http\Requests\Admin\UpdateSubscriptionPlanRequest;
use Modules\Subscription\Services\SubscriptionService;

class AdminSubscriptionPlanController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display all subscription plans for Admin.
     */
    public function index(): View
    {
        $plans = $this->subscriptionService->getAllPlans();

        return view('subscription::admin.plans.index', compact('plans'));
    }

    /**
     * Show form to create a new subscription plan.
     */
    public function create(): View
    {
        return view('subscription::admin.plans.create');
    }

    /**
     * Store a newly created subscription plan in database.
     */
    public function store(StoreSubscriptionPlanRequest $request): RedirectResponse
    {
        $this->subscriptionService->createPlan($request->validated());

        return redirect()->route('admin.plans.index')->with('status', 'plan-created');
    }

    /**
     * Show form to edit an existing subscription plan.
     */
    public function edit(int $id): View
    {
        $plan = $this->subscriptionService->getPlanById($id);

        return view('subscription::admin.plans.edit', compact('plan'));
    }

    /**
     * Update an existing subscription plan in database.
     */
    public function update(UpdateSubscriptionPlanRequest $request, int $id): RedirectResponse
    {
        $this->subscriptionService->updatePlan($id, $request->validated());

        return redirect()->route('admin.plans.index')->with('status', 'plan-updated');
    }

    /**
     * Toggle active/inactive status of a plan.
     */
    public function toggleActive(int $id): RedirectResponse
    {
        $this->subscriptionService->togglePlanStatus($id);

        return redirect()->route('admin.plans.index')->with('status', 'plan-toggled');
    }

    /**
     * Delete a subscription plan.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->subscriptionService->deletePlan($id);

        return redirect()->route('admin.plans.index')->with('status', 'plan-deleted');
    }
}
