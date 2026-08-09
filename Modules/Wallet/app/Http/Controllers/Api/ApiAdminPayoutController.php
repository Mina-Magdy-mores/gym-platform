<?php

namespace Modules\Wallet\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Traits\ApiResponseTrait;
use Modules\Wallet\Models\PayoutRequest;
use Modules\Wallet\Services\WalletService;
use Modules\Wallet\Transformers\PayoutRequestResource;

class ApiAdminPayoutController extends Controller
{
    use ApiResponseTrait;

    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get all trainer payout requests for Admin approval.
     */
    public function index(): JsonResponse
    {
        $payoutRequests = $this->walletService->getAllPayoutRequests();

        return $this->successResponse(
            PayoutRequestResource::collection($payoutRequests),
            'Trainer payout requests fetched successfully.'
        );
    }

    /**
     * Approve trainer payout request via API.
     */
    public function approve(PayoutRequest $payoutRequest): JsonResponse
    {
        try {
            $approved = $this->walletService->approvePayoutRequest($payoutRequest);

            return $this->successResponse(
                new PayoutRequestResource($approved),
                'Payout request approved and settled successfully.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Reject trainer payout request via API.
     */
    public function reject(Request $request, PayoutRequest $payoutRequest): JsonResponse
    {
        try {
            $reason = $request->input('notes', 'Rejected by Admin.');
            $rejected = $this->walletService->rejectPayoutRequest($payoutRequest, $reason);

            return $this->successResponse(
                new PayoutRequestResource($rejected),
                'Payout request rejected and balance restored successfully.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}
