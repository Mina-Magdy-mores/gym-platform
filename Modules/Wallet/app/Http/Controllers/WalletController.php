<?php

namespace Modules\Wallet\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Wallet\Services\WalletService;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display trainer wallet ledger dashboard view.
     */
    public function index(Request $request): View
    {
        $data = $this->walletService->getTrainerWalletData($request->user()->id);

        $wallet = $data['wallet'];
        $transactions = $data['transactions'];

        return view('wallet::index', compact('wallet', 'transactions'));
    }
}
