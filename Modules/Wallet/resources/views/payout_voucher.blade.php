<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payout Settlement Voucher - {{ $voucher_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2d3748;
            margin: 0;
            padding: 30px;
            background-color: #ffffff;
            font-size: 13px;
            line-height: 1.5;
        }
        .invoice-header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #ff5b00;
            padding-bottom: 20px;
        }
        .brand-title {
            font-size: 26px;
            font-weight: 900;
            color: #1a202c;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .brand-accent {
            color: #ff5b00;
        }
        .subtitle {
            font-size: 10px;
            color: #718096;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: #22c55e;
            color: #ffffff;
            margin-top: 6px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .details-cell {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 10px;
            font-weight: 800;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            font-size: 12px;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .item-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
        }
        .item-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }
        .totals-container {
            width: 100%;
            margin-top: 20px;
        }
        .totals-table {
            width: 320px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 10px;
            text-align: right;
        }
        .total-row {
            font-size: 16px;
            font-weight: 900;
            color: #ff5b00;
            border-top: 2px solid #2d3748;
            padding-top: 10px;
        }
        .qr-section {
            margin-top: 40px;
            text-align: center;
            border-top: 1px dashed #cbd5e1;
            padding-top: 20px;
        }
        .qr-container {
            display: inline-block;
            padding: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .footer-note {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="invoice-header">
        <tr>
            <td>
                <div class="brand-title">FIT<span class="brand-accent">CLUB</span></div>
                <div class="subtitle">Official Trainer Earnings Payout Voucher</div>
                <div class="status-badge">{{ $payout->status === 'approved' ? 'PAID & TRANSFERRED' : strtoupper($payout->status) }}</div>
                <div style="font-size: 11px; font-weight: 800; color: #475569; margin-top: 8px;">{{ $voucher_number }}</div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <div class="section-title">ISSUER INFORMATION</div>
                <div style="font-weight: 800; color: #1e293b;">FitClub Gym Enterprise Systems Ltd.</div>
                <div style="font-size: 11px; color: #64748b;">Tax Registration No: <strong>{{ $tax_registration_no }}</strong></div>
                <div style="font-size: 11px; color: #64748b;">Date: <strong>{{ $payout->created_at?->format('Y-m-d H:i') }}</strong></div>
                <div style="font-size: 11px; color: #64748b;">Voucher Ref: <strong>#POUT-{{ $payout->id }}</strong></div>
            </td>
        </tr>
    </table>

    <table class="details-table">
        <tr>
            <td class="details-cell" style="padding-right: 15px;">
                <div class="section-title">BENEFICIARY TRAINER DETAILS:</div>
                <div class="info-box">
                    <strong style="font-size: 14px; color: #0f172a;">Coach {{ $user->name }}</strong><br>
                    <span style="color: #64748b;">{{ $user->email }}</span><br>
                    <span style="color: #64748b;">{{ $user->phone ?? 'Registered Trainer' }}</span>
                </div>
            </td>
            <td class="details-cell" style="padding-left: 15px;">
                <div class="section-title">PAYOUT SETTLEMENT METHOD:</div>
                <div class="info-box">
                    <strong style="color: #ff5b00; font-size: 13px;">{{ $payment_method }}</strong><br>
                    <span style="color: #475569;">Account / Phone: <strong>{{ $account_details }}</strong></span><br>
                    <span style="color: #64748b; font-size: 11px;">Settlement Engine: FitClub Wallet Ledger</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th>Transaction Description</th>
                <th style="text-align: center;">Qty</th>
                <th>Payout Channel</th>
                <th style="text-align: right;">Amount (EGP)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Trainer Earnings Balance Withdrawal Settlement</td>
                <td style="text-align: center;">1</td>
                <td>{{ $payment_method }}</td>
                <td style="text-align: right; font-weight: 800; font-size: 14px;">{{ number_format($amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td>Subtotal Net Payout:</td>
                <td><strong>{{ number_format($amount, 2) }} EGP</strong></td>
            </tr>
            <tr>
                <td>Transfer Fee / Deductions:</td>
                <td><strong>0.00 EGP</strong></td>
            </tr>
            <tr class="total-row">
                <td style="padding-top: 10px;">Total Disbursed:</td>
                <td style="padding-top: 10px;">{{ number_format($amount, 2) }} EGP</td>
            </tr>
        </table>
    </div>

    <div class="qr-section">
        <div class="qr-container">
            {!! $qr_code_svg !!}
        </div>
        <div class="footer-note">
            This is an officially verified digital payout settlement voucher generated by FitClub Gym Enterprise Platform.<br>
            Scannable QR code verifies authentic financial ledger transaction recording.
        </div>
    </div>

</body>
</html>
