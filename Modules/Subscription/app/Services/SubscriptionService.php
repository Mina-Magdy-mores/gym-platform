<?php

namespace Modules\Subscription\Services;

use App\Models\User;
use App\Traits\CacheableServiceTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Subscription\Models\GymRule;
use Modules\Subscription\Models\GymSchedule;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Models\UserSubscription;

class SubscriptionService
{
    use CacheableServiceTrait;

    /**
     * Cache key for active subscription plans.
     */
    protected const CACHE_ACTIVE_PLANS = 'subscription_plans_active';

    /**
     * Get all active subscription plans with safe application caching.
     */
    public function getActivePlans(): Collection
    {
        return $this->rememberSafe(self::CACHE_ACTIVE_PLANS, 86400, function () {
            return SubscriptionPlan::active()->get();
        });
    }

    /**
     * Get active gym rules for UI rendering.
     */
    public function getActiveGymRules(): Collection
    {
        return GymRule::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('rule_number', 'asc')
            ->get();
    }

    /**
     * Get all subscription plans for Admin.
     */
    public function getAllPlans(): Collection
    {
        return SubscriptionPlan::latest()->get();
    }

    /**
     * Get plan by ID or fail.
     */
    public function getPlanById(int $id): SubscriptionPlan
    {
        return SubscriptionPlan::findOrFail($id);
    }

    /**
     * Create a new subscription plan & clear active plans cache.
     */
    public function createPlan(array $data): SubscriptionPlan
    {
        $data['slug'] = Str::slug($data['name']) . '-' . rand(100, 999);
        $data['currency'] = 'EGP';
        $data['spa_access'] = isset($data['spa_access']) && $data['spa_access'];
        $data['is_featured'] = isset($data['is_featured']) && $data['is_featured'];
        $data['is_active'] = true;

        $plan = SubscriptionPlan::create($data);

        $this->clearPlansCache();

        return $plan;
    }

    /**
     * Update an existing subscription plan & clear active plans cache.
     */
    public function updatePlan(int $id, array $data): SubscriptionPlan
    {
        $plan = $this->getPlanById($id);
        $data['spa_access'] = isset($data['spa_access']) && $data['spa_access'];
        $data['is_featured'] = isset($data['is_featured']) && $data['is_featured'];

        $plan->update($data);

        $this->clearPlansCache();

        return $plan;
    }

    /**
     * Toggle active/inactive status of a plan & clear active plans cache.
     */
    public function togglePlanStatus(int $id): SubscriptionPlan
    {
        $plan = $this->getPlanById($id);
        $plan->is_active = ! $plan->is_active;
        $plan->save();

        $this->clearPlansCache();

        return $plan;
    }

    /**
     * Delete a subscription plan & clear active plans cache.
     */
    public function deletePlan(int $id): bool
    {
        $plan = $this->getPlanById($id);
        $deleted = $plan->delete();

        $this->clearPlansCache();

        return $deleted;
    }

    /**
     * Clear subscription plans application cache.
     */
    public function clearPlansCache(): void
    {
        Cache::forget(self::CACHE_ACTIVE_PLANS);
    }

    /**
     * Get gym schedules filtered by target gender with safe application caching.
     */
    public function getGymSchedules(string $gender): Collection
    {
        $cacheKey = "gym_schedules_{$gender}";

        return $this->rememberSafe($cacheKey, 86400, function () use ($gender) {
            return GymSchedule::where('target_gender', $gender)->get();
        });
    }

    /**
     * Get all gym schedules for Admin.
     */
    public function getAllSchedules(): Collection
    {
        return GymSchedule::latest()->get();
    }

    /**
     * Get schedule by ID or fail.
     */
    public function getScheduleById(int $id): GymSchedule
    {
        return GymSchedule::findOrFail($id);
    }

    /**
     * Create a new gym schedule & clear schedules cache.
     */
    public function createSchedule(array $data): GymSchedule
    {
        $data['is_off_day'] = isset($data['is_off_day']) && $data['is_off_day'];

        $schedule = GymSchedule::create($data);

        $this->clearSchedulesCache();

        return $schedule;
    }

    /**
     * Update an existing gym schedule & clear schedules cache.
     */
    public function updateSchedule(int $id, array $data): GymSchedule
    {
        $schedule = $this->getScheduleById($id);
        $data['is_off_day'] = isset($data['is_off_day']) && $data['is_off_day'];

        $schedule->update($data);

        $this->clearSchedulesCache();

        return $schedule;
    }

    /**
     * Delete a gym schedule & clear schedules cache.
     */
    public function deleteSchedule(int $id): bool
    {
        $schedule = $this->getScheduleById($id);
        $deleted = $schedule->delete();

        $this->clearSchedulesCache();

        return $deleted;
    }

    /**
     * Clear gym schedules cache.
     */
    public function clearSchedulesCache(): void
    {
        Cache::forget('gym_schedules_men');
        Cache::forget('gym_schedules_women');
    }

    /**
     * Subscribe user to a plan.
     */
    public function subscribeUser(User $user, int $planId): UserSubscription
    {
        $plan = SubscriptionPlan::findOrFail($planId);

        $startDate = now();
        $endDate = now()->addMonths($plan->duration_months)->addDays($plan->free_days);

        return UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'status' => 'active',
            'price_paid' => $plan->price,
            'remaining_freeze_days' => $plan->freeze_days,
            'remaining_invitations' => $plan->invitations_count,
            'remaining_inbody_scans' => $plan->inbody_scans,
            'remaining_pt_sessions' => $plan->pt_sessions,
            'remaining_kickboxing_classes' => $plan->kickboxing_classes,
            'remaining_nutrition_plans' => $plan->nutrition_plans,
        ]);
    }
}