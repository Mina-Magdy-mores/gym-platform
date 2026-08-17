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

    /**
     * Get voucher receipt data for a payout request.
     */
    public function voucher(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $payoutRequest = \Modules\Wallet\Models\PayoutRequest::with('user')->findOrFail($id);

        if ($user->id !== $payoutRequest->user_id && ! $user->hasRole('admin')) {
            return $this->errorResponse('Unauthorized to view this voucher.', 403);
        }

        return $this->successResponse([
            'voucher_number' => 'VOUCHER-PAY-' . str_pad($payoutRequest->id, 6, '0', STR_PAD_LEFT),
            'trainer_name' => $payoutRequest->user->name,
            'trainer_email' => $payoutRequest->user->email,
            'amount' => (float) $payoutRequest->amount,
            'currency' => 'EGP',
            'payment_method' => strtoupper($payoutRequest->payment_method),
            'account_details' => $payoutRequest->account_details,
            'status' => $payoutRequest->status,
            'requested_at' => $payoutRequest->created_at->toIso8601String(),
            'processed_at' => $payoutRequest->updated_at->toIso8601String(),
        ], 'Payout voucher details fetched successfully.');
    }
}
