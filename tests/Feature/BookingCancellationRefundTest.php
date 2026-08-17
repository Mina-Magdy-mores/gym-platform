<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Services\BookingService;
use Modules\Subscription\Services\SubscriptionService;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BookingCancellationRefundTest extends TestCase
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

    public function test_cancellation_more_than_24h_before_session_restores_pt_session_to_member_balance(): void
    {
        $trainer = User::factory()->create(['is_active' => true]);
        $trainer->assignRole('trainer');

        $member = User::factory()->create(['is_active' => true]);
        $member->assignRole('member');

        $plan = SubscriptionPlan::create([
            'name' => 'Monthly Plan',
            'slug' => 'monthly-plan-cancel',
            'duration_months' => 1,
            'price' => 600,
            'pt_sessions' => 4,
            'is_active' => true,
        ]);

        $sub = $this->subscriptionService->subscribeUser($member, $plan->id);

        // Book session 3 days in the future (eligible for 100% refund / session recovery)
        $booking = $this->bookingService->bookTrainerSession($member, [
            'trainer_id' => $trainer->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '16:00:00',
            'end_time' => '17:00:00',
        ]);

        $this->assertEquals(3, $sub->fresh()->remaining_pt_sessions);

        // Cancel the booking
        $result = $this->bookingService->cancelBooking($booking);

        $this->assertEquals('cancelled', $booking->fresh()->status);
        $this->assertEquals('refunded', $booking->fresh()->refund_status);
        $this->assertEquals('session_restored', $booking->fresh()->refund_method);

        // Assert the PT session balance was restored from 3 back to 4
        $this->assertEquals(4, $sub->fresh()->remaining_pt_sessions);
    }

    public function test_cancellation_within_24h_marks_refund_as_none_to_protect_trainer_schedule(): void
    {
        $trainer = User::factory()->create(['is_active' => true]);
        $trainer->assignRole('trainer');

        $member = User::factory()->create(['is_active' => true]);
        $member->assignRole('member');

        $plan = SubscriptionPlan::create([
            'name' => 'Monthly Plan',
            'slug' => 'monthly-plan-late-cancel',
            'duration_months' => 1,
            'price' => 600,
            'pt_sessions' => 2,
            'is_active' => true,
        ]);

        $sub = $this->subscriptionService->subscribeUser($member, $plan->id);

        // Create booking 2 hours from now
        $booking = Booking::create([
            'user_id' => $member->id,
            'trainer_id' => $trainer->id,
            'booking_date' => now()->format('Y-m-d'),
            'start_time' => now()->addHours(2)->format('H:i:s'),
            'end_time' => now()->addHours(3)->format('H:i:s'),
            'status' => 'confirmed',
            'price' => 0.00,
        ]);
        $sub->decrement('remaining_pt_sessions');

        $this->assertEquals(1, $sub->fresh()->remaining_pt_sessions);

        // Cancel booking within 24h window
        $this->bookingService->cancelBooking($booking);

        $this->assertEquals('cancelled', $booking->fresh()->status);
        $this->assertEquals('none', $booking->fresh()->refund_status);

        // Assert session was NOT restored because it was cancelled too late
        $this->assertEquals(1, $sub->fresh()->remaining_pt_sessions);
    }
}
