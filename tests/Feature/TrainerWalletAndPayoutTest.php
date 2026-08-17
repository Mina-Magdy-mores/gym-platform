<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Subscription\Models\Booking;
use Modules\Wallet\Models\TrainerWallet;
use Modules\Wallet\Services\WalletService;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TrainerWalletAndPayoutTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('trainer', 'web');
        Role::findOrCreate('member', 'web');

        $this->walletService = app(WalletService::class);
    }

    public function test_trainer_wallet_is_credited_85_percent_net_upon_session_booking(): void
    {
        $trainer = User::factory()->create([
            'is_active' => true,
        ]);
        $trainer->assignRole('trainer');

        $member = User::factory()->create(['is_active' => true]);
        $member->assignRole('member');

        $booking = Booking::create([
            'user_id' => $member->id,
            'trainer_id' => $trainer->id,
            'booking_date' => now()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'status' => 'confirmed',
            'price' => 200.00,
        ]);

        $txn = $this->walletService->creditTrainerForSession($booking);

        $this->assertEquals(200.00, $txn->amount);
        $this->assertEquals(30.00, $txn->commission_amount); // 15% of 200 = 30
        $this->assertEquals(170.00, $txn->net_amount);        // 85% of 200 = 170

        $wallet = TrainerWallet::where('user_id', $trainer->id)->first();
        $this->assertNotNull($wallet);
        $this->assertEquals(170.00, (float) $wallet->balance);
        $this->assertEquals(170.00, (float) $wallet->total_earned);
    }

    public function test_trainer_can_request_payout_and_balance_is_frozen_into_pending_payout(): void
    {
        $trainer = User::factory()->create(['is_active' => true]);
        $trainer->assignRole('trainer');

        $wallet = TrainerWallet::create([
            'user_id' => $trainer->id,
            'balance' => 500.00,
            'total_earned' => 500.00,
            'pending_payout' => 0.00,
        ]);

        // Request 300 EGP payout
        $payoutRequest = $this->walletService->requestTrainerPayout(
            $trainer,
            300.00,
            'instapay',
            'user@instapay'
        );

        $this->assertEquals('pending', $payoutRequest->status);
        $this->assertEquals(300.00, $payoutRequest->amount);

        // Assert 300 was moved from balance to pending_payout
        $wallet->refresh();
        $this->assertEquals(200.00, (float) $wallet->balance);
        $this->assertEquals(300.00, (float) $wallet->pending_payout);
    }

    public function test_admin_approving_payout_settles_balance_and_marks_request_approved(): void
    {
        $trainer = User::factory()->create(['is_active' => true]);
        $trainer->assignRole('trainer');

        $wallet = TrainerWallet::create([
            'user_id' => $trainer->id,
            'balance' => 100.00,
            'total_earned' => 500.00,
            'pending_payout' => 400.00,
        ]);

        $payoutRequest = $this->walletService->requestTrainerPayout(
            $trainer,
            100.00,
            'vodafone_cash',
            '01099998888'
        );

        // Approve payout request
        $approvedRequest = $this->walletService->approvePayoutRequest($payoutRequest);

        $this->assertEquals('approved', $approvedRequest->status);

        $wallet->refresh();
        $this->assertEquals(400.00, (float) $wallet->pending_payout);
    }
}
