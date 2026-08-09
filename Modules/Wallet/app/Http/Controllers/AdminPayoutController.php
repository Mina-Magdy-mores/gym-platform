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
     * Display all trainer payout requests for Admin approval.
     */
    public function index(): View
    {
        $payoutRequests = $this->walletService->getAllPayoutRequests();

        return view('wallet::admin_payouts', compact('payoutRequests'));
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