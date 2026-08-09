<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Payment\Models\Payment;
use Modules\Payment\Services\InvoiceService;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
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
     * Display printable HTML tax receipt view with QR Code.
     */
    public function show(Payment $payment): View
    {
        $this->authorizeInvoiceAccess($payment);

        $data = $this->invoiceService->generateInvoiceData($payment);

        return view('payment::invoice', $data);
    }

    /**
     * Download official PDF tax receipt document.
     */
    public function download(Payment $payment): Response
    {
        $this->authorizeInvoiceAccess($payment);

        return $this->invoiceService->downloadPdfInvoice($payment);
    }
}