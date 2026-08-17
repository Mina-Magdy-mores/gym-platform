<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Chat\Models\Conversation;
use Modules\Workout\Models\DietPlan;
use Modules\Workout\Models\WorkoutRoutine;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkoutAndChatApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('trainer', 'web');
        Role::findOrCreate('member', 'web');
    }

    public function test_coach_can_create_workout_routine_via_api(): void
    {
        $trainer = User::factory()->create(['is_active' => true]);
        $trainer->assignRole('trainer');

        $member = User::factory()->create(['is_active' => true]);
        $member->assignRole('member');

        $response = $this->actingAs($trainer, 'sanctum')->postJson('/api/v1/workouts/routines', [
            'user_id' => $member->id,
            'title' => 'Hypertrophy Upper Body',
            'goal' => 'Muscle Building',
            'status' => 'active',
            'exercises' => [
                [
                    'day_name' => 'Day 1 - Chest & Arms',
                    'exercise_name' => 'Barbell Bench Press',
                    'target_muscle' => 'Chest',
                    'sets' => 4,
                    'reps' => '8-10',
                    'rest_seconds' => 90,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Hypertrophy Upper Body');

        $this->assertDatabaseHas('workout_routines', [
            'user_id' => $member->id,
            'trainer_id' => $trainer->id,
            'title' => 'Hypertrophy Upper Body',
        ]);
    }

    public function test_coach_can_create_diet_plan_via_api(): void
    {
        $trainer = User::factory()->create(['is_active' => true]);
        $trainer->assignRole('trainer');

        $member = User::factory()->create(['is_active' => true]);
        $member->assignRole('member');

        $response = $this->actingAs($trainer, 'sanctum')->postJson('/api/v1/workouts/diets', [
            'user_id' => $member->id,
            'title' => 'Clean Bulk 3000 Kcal',
            'daily_calories' => 3000,
            'protein_grams' => 200,
            'carbs_grams' => 350,
            'fats_grams' => 70,
            'status' => 'active',
            'meals' => [
                [
                    'meal_name' => 'Breakfast',
                    'meal_time' => '08:00 AM',
                    'food_items' => '5 Egg Whites + 100g Oats + 1 Banana',
                    'calories' => 650,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Clean Bulk 3000 Kcal');

        $this->assertDatabaseHas('diet_plans', [
            'user_id' => $member->id,
            'trainer_id' => $trainer->id,
            'title' => 'Clean Bulk 3000 Kcal',
        ]);
    }

    public function test_user_can_initialize_chat_and_send_message_via_api(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $member->assignRole('member');

        $trainer = User::factory()->create(['is_active' => true]);
        $trainer->assignRole('trainer');

        // 1. Initialize chat room
        $startResponse = $this->actingAs($member, 'sanctum')->postJson('/api/v1/chats/start', [
            'user_id' => $trainer->id,
        ]);

        $startResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $conversationId = $startResponse->json('data.id');

        // 2. Send real-time chat message
        $msgResponse = $this->actingAs($member, 'sanctum')->postJson("/api/v1/chats/{$conversationId}/messages", [
            'message' => 'Hello Coach, I completed today session!',
        ]);

        $msgResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.message', 'Hello Coach, I completed today session!');

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversationId,
            'sender_id' => $member->id,
            'message' => 'Hello Coach, I completed today session!',
        ]);
    }
}
