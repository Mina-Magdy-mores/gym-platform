<?php

namespace Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payment\Services\PaymentService;

class ApiPaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle incoming payment gateway webhooks (Paymob / Stripe).
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            $response = $this->paymentService->handleGatewayWebhook($request->all());

            return response()->json([
                'status' => $response->isSuccessful ? 'success' : 'processed',
                'message' => $response->message,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}