<?php

namespace Modules\Wallet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Traits\ApiResponseTrait;
use Modules\Wallet\Services\WalletService;
use Modules\Wallet\Transformers\TrainerWalletResource;

class ApiWalletController extends Controller
{
    use ApiResponseTrait;

    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get trainer wallet balance and recent ledger transactions.
     */
    public function trainerWallet(Request $request): JsonResponse
    {
        $data = $this->walletService->getTrainerWalletData($request->user()->id);

        return $this->successResponse(
            new TrainerWalletResource($data),
            'Trainer wallet ledger fetched successfully.'
        );
    }
}
