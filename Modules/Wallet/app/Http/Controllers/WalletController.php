<?php

namespace Modules\Wallet\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Payment\Services\InvoiceService;
use Modules\Wallet\Http\Requests\RequestPayoutRequest;
use Modules\Wallet\Models\PayoutRequest;
use Modules\Wallet\Services\WalletService;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    protected WalletService $walletService;
    protected InvoiceService $invoiceService;

    public function __construct(WalletService $walletService, InvoiceService $invoiceService)
    {
        $this->walletService = $walletService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display trainer wallet ledger dashboard view.
     */
    public function index(Request $request): View
    {
        $data = $this->walletService->getTrainerWalletData($request->user()->id);

        $wallet = $data['wallet'];
        $transactions = $data['transactions'];
        $payoutRequests = $data['payout_requests'];

        return view('wallet::index', compact('wallet', 'transactions', 'payoutRequests'));
    }

    /**
     * Submit a payout request for trainer wallet balance.
     */
    public function store(RequestPayoutRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->walletService->requestTrainerPayout(
                $request->user(),
                (float) $validated['amount'],
                $validated['payment_method'],
                $validated['account_details']
            );

            return redirect()->route('wallet.index')->with('status', 'payout_requested');
        } catch (\Exception $e) {
            return redirect()->route('wallet.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Display printable HTML payout settlement voucher for trainer.
     */
    public function voucher(PayoutRequest $payoutRequest): View
    {
        $user = auth()->user();
        if (! $user->hasRole('admin') && $payoutRequest->user_id !== $user->id) {
            abort(403, 'Unauthorized access to payout voucher.');
        }

        $data = $this->invoiceService->generatePayoutVoucherData($payoutRequest);

        return view('wallet::payout_voucher', $data);
    }

    /**
     * Download official PDF payout settlement voucher document for trainer.
     */
    public function downloadVoucher(PayoutRequest $payoutRequest): Response
    {
        $user = auth()->user();
        if (! $user->hasRole('admin') && $payoutRequest->user_id !== $user->id) {
            abort(403, 'Unauthorized access to payout voucher.');
        }

        return $this->invoiceService->downloadPdfPayoutVoucher($payoutRequest);
    }
}