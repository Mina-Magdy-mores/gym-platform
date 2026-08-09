<?php

namespace Modules\Payment\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Payment\Models\Payment;
use Modules\Wallet\Models\PayoutRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceService
{
    /**
     * Generate structured tax calculation & metadata array for a payment receipt.
     */
    public function generateInvoiceData(Payment $payment): array
    {
        $payment->load(['user', 'plan', 'booking.trainer']);

        $totalAmount = (float) $payment->amount;
        
        // Calculate 14% VAT breakdown (Subtotal + VAT 14% = Total)
        $subtotal = round($totalAmount / 1.14, 2);
        $vatAmount = round($totalAmount - $subtotal, 2);

        $invoiceNumber = 'INV-' . $payment->created_at?->format('Ymd') . '-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT);

        // Determine Purchased Item Description
        if ($payment->booking_id && $payment->booking) {
            $itemDescription = 'Private Personal Trainer Session - Coach ' . ($payment->booking->trainer->name ?? 'Specialist');
        } elseif ($payment->subscription_plan_id && $payment->plan) {
            $itemDescription = 'Gym Membership Plan - ' . $payment->plan->name;
        } else {
            $itemDescription = 'FitClub Fitness Services';
        }

        // Generate Verification Payload & Base64 QR Code
        $verificationUrl = route('invoices.show', $payment->id);
        $qrCodeSvg = QrCode::size(120)->color(255, 91, 0)->generate($verificationUrl);

        return [
            'payment' => $payment,
            'user' => $payment->user,
            'invoice_number' => $invoiceNumber,
            'item_description' => $itemDescription,
            'subtotal' => $subtotal,
            'vat_rate' => '14%',
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'currency' => $payment->currency ?? 'EGP',
            'qr_code_svg' => $qrCodeSvg,
            'verification_url' => $verificationUrl,
            'tax_registration_no' => '394-821-049',
        ];
    }

    /**
     * Generate & Download PDF document stream for payment receipt.
     */
    public function downloadPdfInvoice(Payment $payment)
    {
        $data = $this->generateInvoiceData($payment);

        $pdf = Pdf::loadView('payment::invoice', $data);

        return $pdf->download($data['invoice_number'] . '.pdf');
    }

    /**
     * Generate structured payout voucher metadata array for trainer earnings settlement.
     */
    public function generatePayoutVoucherData(PayoutRequest $payout): array
    {
        $payout->load('user');

        $voucherNumber = 'POUT-' . $payout->created_at?->format('Ymd') . '-' . str_pad($payout->id, 5, '0', STR_PAD_LEFT);
        $verificationUrl = route('payouts.voucher', $payout->id);
        $qrCodeSvg = QrCode::size(120)->color(255, 91, 0)->generate($verificationUrl);

        return [
            'payout' => $payout,
            'user' => $payout->user,
            'voucher_number' => $voucherNumber,
            'amount' => (float) $payout->amount,
            'payment_method' => strtoupper(str_replace('_', ' ', $payout->payment_method)),
            'account_details' => $payout->account_details,
            'qr_code_svg' => $qrCodeSvg,
            'verification_url' => $verificationUrl,
            'tax_registration_no' => '394-821-049',
        ];
    }

    /**
     * Generate & Download PDF document stream for trainer payout settlement voucher.
     */
    public function downloadPdfPayoutVoucher(PayoutRequest $payout)
    {
        $data = $this->generatePayoutVoucherData($payout);

        $pdf = Pdf::loadView('wallet::payout_voucher', $data);

        return $pdf->download($data['voucher_number'] . '.pdf');
    }
}