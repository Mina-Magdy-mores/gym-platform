<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscription\Models\GymRule;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GymRulesAndCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('trainer', 'web');
        Role::findOrCreate('member', 'web');
    }

    public function test_active_gym_rules_are_retrieved_ordered_by_sort_order(): void
    {
        GymRule::create([
            'rule_number' => 2,
            'rule_text' => 'Always re-rack weights after use.',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        GymRule::create([
            'rule_number' => 1,
            'rule_text' => 'Proper athletic attire and clean gym shoes are required.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        GymRule::create([
            'rule_number' => 3,
            'rule_text' => 'Inactive temporary rule.',
            'is_active' => false,
            'sort_order' => 3,
        ]);

        $activeRules = GymRule::getActiveRules();

        $this->assertCount(2, $activeRules);
        $this->assertEquals(1, $activeRules->first()->rule_number);
        $this->assertEquals(2, $activeRules->last()->rule_number);
    }

    public function test_admin_can_create_new_gym_rule_and_persist_to_database(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/admin/gym-rules', [
            'rule_number' => 17,
            'rule_text' => 'No unauthorized photography inside locker rooms.',
            'sort_order' => 17,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/gym-rules');

        $this->assertDatabaseHas('gym_rules', [
            'rule_number' => 17,
            'rule_text' => 'No unauthorized photography inside locker rooms.',
            'is_active' => true,
        ]);
    }
}
