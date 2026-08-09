<?php

namespace Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Payment\Models\Payment;
use Modules\Payment\Services\InvoiceService;
use Modules\Payment\Transformers\InvoiceResource;
use Modules\User\Traits\ApiResponseTrait;

class ApiInvoiceController extends Controller
{
    use ApiResponseTrait;

    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Authorize user access to payment tax receipt.
     */
    protected function authorizeInvoiceAccess(Payment $payment): void
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return;
        }

        if ($payment->user_id === $user->id) {
            return;
        }

        if ($payment->booking_id && $payment->booking && $payment->booking->trainer_id === $user->id) {
            return;
        }

        abort(403, 'Unauthorized access to this tax receipt.');
    }

    /**
     * Get JSON tax receipt metadata and PDF download link for mobile WebView.
     */
    public function show(Payment $payment): JsonResponse
    {
        $this->authorizeInvoiceAccess($payment);

        $data = $this->invoiceService->generateInvoiceData($payment);

        return $this->successResponse(
            new InvoiceResource($data),
            'Invoice data fetched successfully.'
        );
    }

    /**
     * Download official PDF tax receipt binary stream directly via API.
     */
    public function download(Payment $payment)
    {
        $this->authorizeInvoiceAccess($payment);

        return $this->invoiceService->downloadPdfInvoice($payment);
    }
}