<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - Luxuria UAE</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #1a1a1a, #333);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .email-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #bfa133, #d4b846, #bfa133);
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #bfa133;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #bfa133;
            border-radius: 50%;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .email-title {
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .email-subtitle {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
        }

        .email-body {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 20px;
        }

        .confirmation-message {
            background: linear-gradient(135deg, #e8f5e8, #f0f8f0);
            border-left: 4px solid #28a745;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .confirmation-message h3 {
            color: #155724;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .confirmation-message p {
            color: #155724;
            margin: 0;
        }

        .invoice-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .invoice-title {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.1rem;
            color: #bfa133;
        }

        .detail-label {
            color: #6c757d;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
        }

        .coupon-info {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border: 2px dashed #bfa133;
        }

        .coupon-title {
            color: #856404;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
        }

        .coupon-code {
            background: white;
            border: 2px solid #bfa133;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 1.3rem;
            font-weight: bold;
            color: #bfa133;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        .coupon-instructions {
            color: #856404;
            text-align: center;
            font-size: 0.95rem;
        }

        .next-steps {
            background: #e7f3ff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .next-steps-title {
            color: #0056b3;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .steps-list {
            color: #0056b3;
            margin: 0;
            padding-left: 20px;
        }

        .steps-list li {
            margin-bottom: 8px;
        }

        .action-buttons {
            text-align: center;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 0 10px 10px 0;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #bfa133, #d4b846);
            color: #000;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .footer-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #bfa133;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .footer-text {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .contact-info {
            color: #6c757d;
            font-size: 0.85rem;
        }

        .contact-info a {
            color: #bfa133;
            text-decoration: none;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }

            .email-header, .email-body, .email-footer {
                padding: 25px 20px;
            }

            .email-title {
                font-size: 1.5rem;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Email Header -->
        <div class="email-header">
            <div class="logo">LUXURIA UAE</div>
            <div class="success-icon">✓</div>
            <h1 class="email-title">Payment Successful!</h1>
            <p class="email-subtitle">Your transaction has been completed successfully</p>
        </div>

        <!-- Email Body -->
        <div class="email-body">
            <div class="greeting">
                Hello {{ $customerName ?? 'Valued Customer' }},
            </div>

            <div class="confirmation-message">
                <h3>🎉 Great news!</h3>
                <p>Your payment has been processed successfully and your discount coupon is now ready to use.</p>
            </div>

            <!-- Invoice Details -->
            <div class="invoice-details">
                <h2 class="invoice-title">Transaction Details</h2>

                <div class="detail-row">
                    <span class="detail-label">Invoice Number:</span>
                    <span class="detail-value">{{ $invoiceNumber }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Coupon Name:</span>
                    <span class="detail-value">{{ $couponName }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Purchase Date:</span>
                    <span class="detail-value">{{ $paidAt ? $paidAt->format('M d, Y H:i') : 'Just now' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value">Credit Card</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">{{ $amount }} {{ $currency }}</span>
                </div>
            </div>

            <!-- Coupon Information -->
            <div class="coupon-info">
                <h3 class="coupon-title">🎟️ Your Discount Coupon</h3>
                @if(isset($couponDetails['code']))
                <div class="coupon-code">{{ $couponDetails['code'] }}</div>
                @else
                <div class="coupon-code">LUXURIA{{ strtoupper(substr($invoiceNumber, -6)) }}</div>
                @endif
                <p class="coupon-instructions">
                    Use this code at checkout to apply your discount on your next luxury car rental.
                </p>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <h3 class="next-steps-title">What's Next?</h3>
                <ol class="steps-list">
                    <li>Keep this email for your records</li>
                    <li>Browse our luxury vehicle collection</li>
                    <li>Apply your coupon code at checkout</li>
                    <li>Enjoy your savings on premium car rentals</li>
                </ol>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ config('app.url') }}" class="btn btn-primary">Browse Cars</a>
                <a href="{{ config('app.url') }}/coupons" class="btn btn-secondary">View More Coupons</a>
            </div>

            <p style="color: #6c757d; font-size: 0.9rem; margin-top: 20px;">
                If you have any questions about your purchase or need assistance, please don't hesitate to contact our support team.
            </p>
        </div>

        <!-- Email Footer -->
        <div class="email-footer">
            <div class="footer-logo">LUXURIA UAE</div>
            <p class="footer-text">Premium Luxury Car Rental Services</p>
            <p class="footer-text">Thank you for choosing Luxuria UAE for your luxury transportation needs.</p>

            <div class="contact-info">
                <p>Need help? Contact us at <a href="mailto:support@luxuriauae.com">support@luxuriauae.com</a></p>
                <p>Visit our website: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a></p>
            </div>
        </div>
    </div>
</body>
</html>
