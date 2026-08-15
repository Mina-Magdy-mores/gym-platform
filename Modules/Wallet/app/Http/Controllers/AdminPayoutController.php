<?php

namespace Modules\Wallet\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Wallet\Models\PayoutRequest;
use Modules\Wallet\Services\WalletService;

class AdminPayoutController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display all trainer wallets, earnings ledger, and payout requests for Admin.
     */
    public function index(): View
    {
        $payoutRequests = $this->walletService->getAllPayoutRequests();
        $trainerWallets = \Modules\Wallet\Models\TrainerWallet::with('user.media')->get();
        $recentTransactions = \Modules\Wallet\Models\WalletTransaction::with(['wallet.user', 'booking.user'])
            ->latest()
            ->take(15)
            ->get();

        $stats = [
            'totalTrainerBalance' => $trainerWallets->sum('balance'),
            'totalEarned'         => $trainerWallets->sum('total_earned'),
            'pendingPayoutsCount' => $payoutRequests->where('status', 'pending')->count(),
            'pendingPayoutsSum'   => $payoutRequests->where('status', 'pending')->sum('amount'),
            'settledPayoutsSum'   => $payoutRequests->where('status', 'approved')->sum('amount'),
        ];

        return view('wallet::admin_payouts', compact('payoutRequests', 'trainerWallets', 'recentTransactions', 'stats'));
    }

    /**
     * Approve trainer payout request & settle frozen balance.
     */
    public function approve(PayoutRequest $payoutRequest): RedirectResponse
    {
        try {
            $this->walletService->approvePayoutRequest($payoutRequest);

            return redirect()->back()->with('status', 'payout_approved');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject trainer payout request & restore frozen balance back to wallet.
     */
    public function reject(Request $request, PayoutRequest $payoutRequest): RedirectResponse
    {
        try {
            $reason = $request->input('notes', 'Rejected by Admin.');
            $this->walletService->rejectPayoutRequest($payoutRequest, $reason);

            return redirect()->back()->with('status', 'payout_rejected');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}