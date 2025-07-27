<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            font-size: 14px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #d4af37;
            padding-bottom: 20px;
        }

        .company-info {
            flex: 1;
        }

        .company-name {
            font-size: 32px;
            font-weight: bold;
            color: #d4af37;
            margin-bottom: 5px;
        }

        .company-tagline {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .company-address {
            font-size: 12px;
            color: #777;
            line-height: 1.4;
        }

        .invoice-info {
            text-align: right;
            flex: 1;
        }

        .invoice-title {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .invoice-number {
            font-size: 18px;
            color: #d4af37;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .invoice-date {
            font-size: 14px;
            color: #666;
        }

        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .billing-info {
            flex: 1;
            margin-right: 40px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 5px;
        }

        .customer-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #d4af37;
        }

        .customer-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .customer-email {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .payment-status {
            text-align: right;
            flex: 1;
        }

        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }

        .status-failed {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .items-table thead {
            background: #d4af37;
            color: white;
        }

        .items-table th,
        .items-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .items-table th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .items-table tbody tr:hover {
            background: #f9f9f9;
        }

        .amount-cell {
            text-align: right;
            font-weight: bold;
        }

        .total-section {
            margin-top: 30px;
            text-align: right;
        }

        .total-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 8px 15px;
            border-bottom: 1px solid #eee;
        }

        .total-table .total-row {
            background: #d4af37;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .total-table .total-row td {
            border-bottom: none;
        }

        .payment-details {
            margin-top: 40px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #d4af37;
        }

        .payment-method {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            border-top: 2px solid #d4af37;
            padding-top: 20px;
            color: #666;
            font-size: 12px;
        }

        .footer-title {
            font-weight: bold;
            color: #d4af37;
            margin-bottom: 10px;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #17a2b8;
        }

        .notes-title {
            font-weight: bold;
            color: #17a2b8;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">Luxuria UAE</div>
                <div class="company-tagline">Premium Car Rental & Luxury Services</div>
                <div class="company-address">
                    Dubai, United Arab Emirates<br>
                    Phone: +971 XX XXX XXXX<br>
                    Email: info@luxuria-uae.com
                </div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                <div class="invoice-date">Date: {{ \Carbon\Carbon::parse($invoice->created_at)->format('F d, Y') }}</div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="billing-info">
                <div class="section-title">Bill To</div>
                <div class="customer-info">
                    <div class="customer-name">{{ $invoice->customer_name ?? $invoice->user->name ?? 'N/A' }}</div>
                    <div class="customer-email">{{ $invoice->customer_email ?? $invoice->user->email ?? 'N/A' }}</div>
                    @if($invoice->user && $invoice->user->pointsys_customer_id)
                    <div style="font-size: 12px; color: #888; margin-top: 5px;">
                        Customer ID: {{ $invoice->user->pointsys_customer_id }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="payment-status">
                <div class="section-title">Payment Status</div>
                <div class="status-badge status-{{ strtolower($invoice->payment_status) }}">
                    {{ ucfirst($invoice->payment_status) }}
                </div>
                @if($invoice->paid_at)
                <div style="margin-top: 10px; font-size: 12px; color: #666;">
                    Paid on: {{ \Carbon\Carbon::parse($invoice->paid_at)->format('F d, Y H:i') }}
                </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Coupon Code</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $invoice->coupon_name ?? 'Coupon Purchase' }}</strong>
                        <br>
                        <small style="color: #666;">Digital Coupon - Luxuria UAE Services</small>
                    </td>
                    <td>
                        @if($invoice->coupon_code)
                            <code style="background: #f1f1f1; padding: 2px 6px; border-radius: 4px; font-family: monospace;">
                                {{ $invoice->coupon_code }}
                            </code>
                        @else
                            <em style="color: #999;">To be assigned</em>
                        @endif
                    </td>
                    <td>1</td>
                    <td class="amount-cell">{{ number_format($invoice->amount, 2) }} {{ strtoupper($invoice->currency) }}</td>
                    <td class="amount-cell">{{ number_format($invoice->amount, 2) }} {{ strtoupper($invoice->currency) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="amount-cell">{{ number_format($invoice->amount, 2) }} {{ strtoupper($invoice->currency) }}</td>
                </tr>
                <tr>
                    <td>Tax (0%):</td>
                    <td class="amount-cell">0.00 {{ strtoupper($invoice->currency) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total:</td>
                    <td class="amount-cell">{{ number_format($invoice->amount, 2) }} {{ strtoupper($invoice->currency) }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Details -->
        @if($invoice->payment_method)
        <div class="payment-details">
            <div class="payment-method">Payment Method: {{ ucfirst($invoice->payment_method) }}</div>
            @if($invoice->payment_details)
                <div style="font-size: 12px; color: #666;">
                    Transaction processed securely via Stripe Payment Gateway
                </div>
            @endif
        </div>
        @endif

        <!-- Notes -->
        <div class="notes">
            <div class="notes-title">Terms & Conditions</div>
            <div style="font-size: 12px; line-height: 1.5;">
                • This invoice is automatically generated for digital coupon purchases.<br>
                • Coupons are valid for use at Luxuria UAE services as per individual coupon terms.<br>
                • For support or inquiries, please contact our customer service team.<br>
                • Payment processing is handled securely through our payment partners.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-title">Thank you for choosing Luxuria UAE!</div>
            <div>
                This is a computer-generated invoice. No signature is required.<br>
                For questions about this invoice, please contact us at info@luxuria-uae.com
            </div>
        </div>
    </div>
</body>
</html>
