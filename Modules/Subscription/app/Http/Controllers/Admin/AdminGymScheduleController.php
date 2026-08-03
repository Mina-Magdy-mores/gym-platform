<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Subscription\Http\Requests\Admin\StoreGymScheduleRequest;
use Modules\Subscription\Http\Requests\Admin\UpdateGymScheduleRequest;
use Modules\Subscription\Services\SubscriptionService;

class AdminGymScheduleController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display all gym schedules for Admin.
     */
    public function index(): View
    {
        $schedules = $this->subscriptionService->getAllSchedules();

        return view('subscription::admin.schedules.index', compact('schedules'));
    }

    /**
     * Show form to create a new gym schedule.
     */
    public function create(): View
    {
        return view('subscription::admin.schedules.create');
    }

    /**
     * Store a newly created gym schedule in database.
     */
    public function store(StoreGymScheduleRequest $request): RedirectResponse
    {
        $this->subscriptionService->createSchedule($request->validated());

        return redirect()->route('admin.schedules.index')->with('status', 'schedule-created');
    }

    /**
     * Show form to edit an existing gym schedule.
     */
    public function edit(int $id): View
    {
        $schedule = $this->subscriptionService->getScheduleById($id);

        return view('subscription::admin.schedules.edit', compact('schedule'));
    }

    /**
     * Update an existing gym schedule in database.
     */
    public function update(UpdateGymScheduleRequest $request, int $id): RedirectResponse
    {
        $this->subscriptionService->updateSchedule($id, $request->validated());

        return redirect()->route('admin.schedules.index')->with('status', 'schedule-updated');
    }

    /**
     * Delete a gym schedule.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->subscriptionService->deleteSchedule($id);

        return redirect()->route('admin.schedules.index')->with('status', 'schedule-deleted');
    }
}
