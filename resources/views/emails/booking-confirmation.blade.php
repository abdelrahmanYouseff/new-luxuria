<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Luxuria UAE</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1a1a1a 0%, #333333 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
            letter-spacing: 2px;
        }
        .content {
            padding: 40px 30px;
        }
        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .success-message h2 {
            color: #155724;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .success-message p {
            color: #155724;
            margin: 0;
            font-size: 16px;
        }
        .booking-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .booking-details h3 {
            color: #1a1a1a;
            margin: 0 0 20px 0;
            font-size: 20px;
            border-bottom: 2px solid #gold;
            padding-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
        }
        .detail-value {
            color: #1a1a1a;
            text-align: right;
            flex: 1;
        }
        .vehicle-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .vehicle-info h3 {
            color: #856404;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .vehicle-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .vehicle-detail {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .vehicle-detail span:first-child {
            font-weight: 600;
            color: #495057;
        }
        .vehicle-detail span:last-child {
            color: #1a1a1a;
        }
        .total-amount {
            background-color: #1a1a1a;
            color: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .total-amount h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #ffd700;
        }
        .footer {
            background-color: #1a1a1a;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .footer h4 {
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .contact-info {
            margin-bottom: 20px;
        }
        .contact-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            color: #ffd700;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }
        .social-links a:hover {
            text-decoration: underline;
        }
        .important-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .important-note h4 {
            color: #856404;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .important-note ul {
            margin: 0;
            padding-left: 20px;
            color: #856404;
        }
        .important-note li {
            margin-bottom: 5px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 0;
                box-shadow: none;
            }
            .content {
                padding: 20px 15px;
            }
            .header {
                padding: 20px 15px;
            }
            .vehicle-details {
                grid-template-columns: 1fr;
            }
            .detail-row {
                flex-direction: column;
                text-align: left;
            }
            .detail-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('images_car/new-logo3.png') }}" alt="Luxuria UAE" class="logo">
            <h1>LUXURIA UAE</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Success Message -->
            <div class="success-message">
                <h2>🎉 Booking Confirmed!</h2>
                <p>Your vehicle reservation has been successfully confirmed and payment has been processed.</p>
            </div>

            <!-- Booking Details -->
            <div class="booking-details">
                <h3>📋 Booking Information</h3>
                <div class="detail-row">
                    <span class="detail-label">Booking ID:</span>
                    <span class="detail-value">#{{ $bookingId }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Reservation UID:</span>
                    <span class="detail-value">{{ $externalUid ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer Name:</span>
                    <span class="detail-value">{{ $customerName }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Booking Date:</span>
                    <span class="detail-value">{{ $bookingDate }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pickup Date:</span>
                    <span class="detail-value">{{ $startDate }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Return Date:</span>
                    <span class="detail-value">{{ $endDate }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pickup Location:</span>
                    <span class="detail-value">{{ $pickupLocation }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Drop-off Location:</span>
                    <span class="detail-value">{{ $dropoffLocation }}</span>
                </div>
            </div>

            <!-- Vehicle Information -->
            <div class="vehicle-info">
                <h3>🚗 Vehicle Details</h3>
                <div class="vehicle-details">
                    <div class="vehicle-detail">
                        <span>Make & Model:</span>
                        <span>{{ $vehicleName }}</span>
                    </div>
                    <div class="vehicle-detail">
                        <span>Year:</span>
                        <span>{{ $vehicle->year ?? 'N/A' }}</span>
                    </div>
                    <div class="vehicle-detail">
                        <span>Color:</span>
                        <span>{{ $vehicle->color ?? 'N/A' }}</span>
                    </div>
                    <div class="vehicle-detail">
                        <span>Category:</span>
                        <span>{{ $vehicle->category ?? 'N/A' }}</span>
                    </div>
                    <div class="vehicle-detail">
                        <span>Transmission:</span>
                        <span>{{ $vehicle->transmission ?? 'Automatic' }}</span>
                    </div>
                    <div class="vehicle-detail">
                        <span>Seats:</span>
                        <span>{{ $vehicle->seats ?? '5' }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="total-amount">
                <h3>Total Amount Paid</h3>
                <div class="amount">{{ number_format($totalAmount, 2) }} {{ $currency }}</div>
            </div>

            <!-- Important Notes -->
            <div class="important-note">
                <h4>📝 Important Information</h4>
                <ul>
                    <li>Please arrive 15 minutes before your pickup time</li>
                    <li>Bring your driving license and passport/Emirates ID</li>
                    <li>Vehicle will be thoroughly cleaned and sanitized</li>
                    <li>Full tank of fuel included</li>
                    <li>24/7 roadside assistance available</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <h4>Need Help?</h4>
            <div class="contact-info">
                <p>📞 Phone: +971 50 123 4567</p>
                <p>📧 Email: support@luxuriauae.com</p>
                <p>🌐 Website: www.luxuriauae.com</p>
            </div>
            <div class="social-links">
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">Twitter</a>
                <a href="#">WhatsApp</a>
            </div>
            <p style="margin-top: 20px; font-size: 12px; opacity: 0.8;">
                Thank you for choosing Luxuria UAE for your luxury car rental needs!
            </p>
        </div>
    </div>
</body>
</html>
