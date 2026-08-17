<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Models\UserSubscription;
use Modules\Subscription\Services\BookingService;
use Modules\Subscription\Services\SubscriptionService;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BookingCollisionTest extends TestCase
{
    use RefreshDatabase;

    protected BookingService $bookingService;
    protected SubscriptionService $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('trainer', 'web');
        Role::findOrCreate('member', 'web');

        $this->bookingService = app(BookingService::class);
        $this->subscriptionService = app(SubscriptionService::class);
    }

    public function test_member_can_book_trainer_session_using_remaining_pt_sessions(): void
    {
        $trainer = User::factory()->create(['is_active' => true]);
        $trainer->assignRole('trainer');

        $member = User::factory()->create(['is_active' => true]);
        $member->assignRole('member');

        $plan = SubscriptionPlan::create([
            'name' => 'Monthly Plan',
            'slug' => 'monthly-plan',
            'duration_months' => 1,
            'price' => 500,
            'pt_sessions' => 3,
            'is_active' => true,
        ]);

        $sub = $this->subscriptionService->subscribeUser($member, $plan->id);

        $this->assertEquals(3, $sub->remaining_pt_sessions);

        $booking = $this->bookingService->bookTrainerSession($member, [
            'trainer_id' => $trainer->id,
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'notes' => 'Chest and Triceps workout',
        ]);

        $this->assertNotNull($booking);
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals($trainer->id, $booking->trainer_id);
        $this->assertEquals($member->id, $booking->user_id);

        // Assert 1 PT session was deducted
        $this->assertEquals(2, $sub->fresh()->remaining_pt_sessions);
    }

    public function test_pessimistic_lock_prevents_overlapping_session_bookings_for_same_trainer(): void
    {
        $trainer = User::factory()->create(['is_active' => true]);
        $trainer->assignRole('trainer');

        $memberA = User::factory()->create(['is_active' => true]);
        $memberA->assignRole('member');

        $memberB = User::factory()->create(['is_active' => true]);
        $memberB->assignRole('member');

        $plan = SubscriptionPlan::create([
            'name' => 'Plan with PT',
            'slug' => 'plan-pt',
            'duration_months' => 1,
            'price' => 500,
            'pt_sessions' => 5,
            'is_active' => true,
        ]);

        $this->subscriptionService->subscribeUser($memberA, $plan->id);
        $this->subscriptionService->subscribeUser($memberB, $plan->id);

        $bookingDate = now()->addDays(3)->format('Y-m-d');

        // Member A books 10:00 to 11:00
        $this->bookingService->bookTrainerSession($memberA, [
            'trainer_id' => $trainer->id,
            'booking_date' => $bookingDate,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);

        // Member B attempts to book overlapping interval 10:30 to 11:30 for the same trainer
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The selected trainer is already booked for this specific time slot.');

        $this->bookingService->bookTrainerSession($memberB, [
            'trainer_id' => $trainer->id,
            'booking_date' => $bookingDate,
            'start_time' => '10:30:00',
            'end_time' => '11:30:00',
        ]);
    }
}
