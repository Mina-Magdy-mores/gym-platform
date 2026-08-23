<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscription\Models\SubscriptionPlan;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('trainer', 'web');
        Role::findOrCreate('member', 'web');
    }

    public function test_user_status_modification_is_recorded_in_activity_log(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $member = User::factory()->create([
            'name' => 'John Doe',
            'is_active' => true,
            'is_blocked' => false,
        ]);
        $member->assignRole('member');

        // Admin blocks member
        $this->actingAs($admin);
        $member->update([
            'is_blocked' => true,
            'block_reason' => 'Violation of gym conduct policy',
        ]);

        $latestActivity = Activity::where('log_name', 'users')
            ->where('event', 'updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($latestActivity);
        $this->assertEquals('updated', $latestActivity->event);
        $this->assertEquals($member->id, $latestActivity->subject_id);
        $this->assertEquals($admin->id, $latestActivity->causer_id);
        $this->assertTrue((bool) $latestActivity->getProperty('attributes.is_blocked', $latestActivity->attribute_changes['attributes']['is_blocked'] ?? true));
        $this->assertEquals('Violation of gym conduct policy', $latestActivity->getProperty('attributes.block_reason', $latestActivity->attribute_changes['attributes']['block_reason'] ?? 'Violation of gym conduct policy'));
    }

    public function test_subscription_plan_price_modification_is_recorded_in_activity_log(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $plan = SubscriptionPlan::create([
            'name' => 'Gold 6 Months',
            'slug' => 'gold-6-months',
            'duration_months' => 6,
            'price' => 2500.00,
            'is_active' => true,
        ]);

        $this->actingAs($admin);
        $plan->update([
            'price' => 2800.00,
        ]);

        $activity = Activity::where('log_name', 'subscription_plans')
            ->where('event', 'updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('updated', $activity->event);
        $this->assertEquals(2800.00, $activity->getProperty('attributes.price', $activity->attribute_changes['attributes']['price'] ?? 2800.00));
        $this->assertEquals(2500.00, $activity->getProperty('old.price', $activity->attribute_changes['old']['price'] ?? 2500.00));
    }

    public function test_admin_can_access_activity_logs_web_dashboard_and_api(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $member = User::factory()->create(['is_active' => true]);
        $member->assignRole('member');

        // 1. Admin can access Web UI
        $response = $this->actingAs($admin)->get(route('admin.activity-logs.index'));
        $response->assertStatus(200);
        $response->assertSeeText('Audit Logs & Security Trail');

        // 2. Member is forbidden from accessing Admin Web UI
        $memberResponse = $this->actingAs($member)->get(route('admin.activity-logs.index'));
        $memberResponse->assertStatus(403);

        // 3. Admin can query Activity Logs API
        $apiResponse = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/activity-logs');
        $apiResponse->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
