<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Models\UserSubscription;
use Modules\Subscription\Services\SubscriptionService;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SubscriptionPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('trainer', 'web');
        Role::findOrCreate('member', 'web');

        $this->subscriptionService = app(SubscriptionService::class);
    }

    public function test_member_can_subscribe_to_plan_and_all_benefit_quotas_are_initialized(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');

        $plan = SubscriptionPlan::create([
            'name' => 'VIP Annual Elite',
            'slug' => 'vip-annual-elite',
            'description' => 'Full access with all perks',
            'duration_months' => 12,
            'price' => 5000.00,
            'currency' => 'EGP',
            'free_days' => 30,
            'freeze_days' => 60,
            'invitations_count' => 10,
            'inbody_scans' => 12,
            'pt_sessions' => 6,
            'kickboxing_classes' => 8,
            'nutrition_plans' => 4,
            'spa_access' => true,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $subscription = $this->subscriptionService->subscribeUser($user, $plan->id);

        $this->assertNotNull($subscription);
        $this->assertEquals('active', $subscription->status);
        $this->assertEquals(5000.00, $subscription->price_paid);
        $this->assertEquals(60, $subscription->remaining_freeze_days);
        $this->assertEquals(10, $subscription->remaining_invitations);
        $this->assertEquals(12, $subscription->remaining_inbody_scans);
        $this->assertEquals(6, $subscription->remaining_pt_sessions);
        $this->assertEquals(8, $subscription->remaining_kickboxing_classes);
        $this->assertEquals(4, $subscription->remaining_nutrition_plans);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    public function test_upgrade_plan_within_seven_days_calculates_price_difference(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('member');

        $basicPlan = SubscriptionPlan::create([
            'name' => 'Basic 3 Months',
            'slug' => 'basic-3-months',
            'duration_months' => 3,
            'price' => 1500.00,
            'currency' => 'EGP',
            'freeze_days' => 10,
            'invitations_count' => 2,
            'inbody_scans' => 2,
            'pt_sessions' => 1,
            'kickboxing_classes' => 0,
            'nutrition_plans' => 0,
            'is_active' => true,
        ]);

        $vipPlan = SubscriptionPlan::create([
            'name' => 'VIP 12 Months',
            'slug' => 'vip-12-months',
            'duration_months' => 12,
            'price' => 4000.00,
            'currency' => 'EGP',
            'freeze_days' => 45,
            'invitations_count' => 6,
            'inbody_scans' => 6,
            'pt_sessions' => 4,
            'kickboxing_classes' => 4,
            'nutrition_plans' => 2,
            'is_active' => true,
        ]);

        // Subscribe to basic plan today
        $this->subscriptionService->subscribeUser($user, $basicPlan->id);

        // Prepare upgrade within 7 days
        $action = $this->subscriptionService->prepareSubscriptionAction($user, $vipPlan);

        $this->assertEquals('upgrade', $action['action']);
        $this->assertEquals(2500.00, $action['amount_to_pay']); // 4000 - 1500 = 2500
    }
}
