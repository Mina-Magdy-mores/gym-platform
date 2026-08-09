<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - {{ $invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #111; padding: 20px; font-size: 13px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0,0,0,0.15); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ff5b00; padding-bottom: 20px; }
        .logo { font-size: 26px; font-weight: 900; color: #111; }
        .logo span { color: #ff5b00; }
        .badge { background: #ff5b00; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        .table th { background: #f8f9fa; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        .summary { float: right; width: 300px; margin-top: 20px; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; }
        .summary-row.total { font-size: 16px; font-weight: 900; color: #ff5b00; border-top: 2px solid #111; padding-top: 10px; }
        .footer { margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; display: flex; justify-content: space-between; align-items: center; }
        .qr-code { text-align: right; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div>
                <div class="logo">FIT<span>CLUB</span></div>
                <div style="font-size: 10px; color: #666; margin-top: 4px;">OFFICIAL ELECTRONIC TAX INVOICE</div>
            </div>
            <div>
                <span class="badge">Paid Tax Receipt</span>
                <div style="font-size: 11px; margin-top: 8px; font-family: monospace;"><b>{{ $invoice_number }}</b></div>
            </div>
        </div>

        <div class="details-grid">
            <div>
                <strong style="color: #666; font-size: 10px; text-transform: uppercase;">Customer Details:</strong>
                <div style="font-size: 14px; font-weight: bold; margin-top: 4px;">{{ $user->name ?? 'Gym Member' }}</div>
                <div style="color: #555;">{{ $user->email }}</div>
                <div style="color: #555;">{{ $user->phone ?? '+201000000000' }}</div>
            </div>

            <div style="text-align: right;">
                <strong style="color: #666; font-size: 10px; text-transform: uppercase;">Issuer Tax Info:</strong>
                <div style="font-size: 13px; font-weight: bold; margin-top: 4px;">FitClub Gym Enterprise Systems Ltd.</div>
                <div style="color: #555;">Tax Registration No: <b>{{ $tax_registration_no }}</b></div>
                <div style="color: #555;">Date: {{ $payment->created_at?->format('Y-m-d H:i') }}</div>
                <div style="color: #555;">Gateway Ref: <b>#{{ $payment->transaction_id }}</b></div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Qty</th>
                    <th>Payment Gateway</th>
                    <th style="text-align: right;">Amount (EGP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>{{ $item_description }}</b></td>
                    <td>1</td>
                    <td style="text-transform: uppercase;">{{ $payment->gateway }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <span>Subtotal (Net Excl. Tax):</span>
                <span>{{ number_format($subtotal, 2) }} {{ $currency }}</span>
            </div>
            <div class="summary-row">
                <span>VAT Tax ({{ $vat_rate }}):</span>
                <span>{{ number_format($vat_amount, 2) }} {{ $currency }}</span>
            </div>
            <div class="summary-row total">
                <span>Total Paid:</span>
                <span>{{ number_format($total_amount, 2) }} {{ $currency }}</span>
            </div>
        </div>
        <div style="clear: both;"></div>

        <div class="footer">
            <div>
                <p style="font-size: 10px; color: #888; max-width: 400px;">
                    This is an officially verified electronic tax invoice generated automatically by FitClub Gym Enterprise Platform. Scannable QR code verifies authentic transaction ledger recording.
                </p>
            </div>
            <div class="qr-code">
                {!! $qr_code_svg !!}
                <div style="font-size: 9px; color: #888; margin-top: 4px;">Scan to Verify Invoice</div>
            </div>
        </div>
    </div>
</body>
</html>
