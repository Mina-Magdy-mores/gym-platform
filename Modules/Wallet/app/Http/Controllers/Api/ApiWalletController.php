<?php

namespace Modules\Wallet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Traits\ApiResponseTrait;
use Modules\Wallet\Http\Requests\RequestPayoutRequest;
use Modules\Wallet\Services\WalletService;
use Modules\Wallet\Transformers\PayoutRequestResource;
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
     * Get trainer wallet balance, pending payouts, and recent ledger transactions.
     */
    public function trainerWallet(Request $request): JsonResponse
    {
        $data = $this->walletService->getTrainerWalletData($request->user()->id);

        return $this->successResponse(
            new TrainerWalletResource($data),
            'Trainer wallet ledger fetched successfully.'
        );
    }

    /**
     * Submit trainer payout request via API.
     */
    public function requestPayout(RequestPayoutRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $payoutRequest = $this->walletService->requestTrainerPayout(
                $request->user(),
                (float) $validated['amount'],
                $validated['payment_method'],
                $validated['account_details']
            );

            return $this->successResponse(
                new PayoutRequestResource($payoutRequest),
                'Payout request submitted successfully. Amount frozen pending admin approval.',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}
