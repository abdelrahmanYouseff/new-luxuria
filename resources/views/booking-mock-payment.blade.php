<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Payment - Vehicle Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }

        .payment-header {
            background: linear-gradient(135deg, #bfa133, #d4b852);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .payment-body {
            padding: 2rem;
        }

        .booking-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .detail-row:last-child {
            margin-bottom: 0;
            font-weight: bold;
            font-size: 1.1rem;
            border-top: 1px solid #dee2e6;
            padding-top: 0.5rem;
            margin-top: 1rem;
        }

        .mock-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .btn-pay {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-pay:hover {
            background: linear-gradient(135deg, #218838, #1ba085);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .btn-cancel {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #545b62;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <div class="payment-header">
            <div class="mb-3">
                <i class="bi bi-credit-card" style="font-size: 3rem;"></i>
            </div>
            <h3 class="mb-0">Complete Your Booking</h3>
            <p class="mb-0 opacity-75">Secure Payment Processing</p>
        </div>

        <div class="payment-body">
            <div class="mock-notice">
                <i class="bi bi-info-circle text-warning me-2"></i>
                <strong>Testing Mode:</strong> This is a simulated payment for demonstration purposes.
            </div>

            <div class="booking-details">
                <h5 class="mb-3">
                    <i class="bi bi-car-front me-2 text-primary"></i>
                    Booking Details
                </h5>

                <div class="detail-row">
                    <span>Vehicle:</span>
                    <span><strong>{{ $booking->vehicle->make }} {{ $booking->vehicle->model }}</strong></span>
                </div>

                <div class="detail-row">
                    <span>Pickup Emirate:</span>
                    <span>{{ $booking->emirate }}</span>
                </div>

                <div class="detail-row">
                    <span>Start Date:</span>
                    <span>{{ $booking->start_date->format('d/m/Y') }}</span>
                </div>

                <div class="detail-row">
                    <span>End Date:</span>
                    <span>{{ $booking->end_date->format('d/m/Y') }}</span>
                </div>

                <div class="detail-row">
                    <span>Total Days:</span>
                    <span>{{ $booking->total_days }} days</span>
                </div>

                <div class="detail-row">
                    <span>Pricing Type:</span>
                    <span>{{ $booking->formatted_pricing_type }}</span>
                </div>

                <div class="detail-row">
                    <span><i class="bi bi-cash-coin me-1"></i> Total Amount:</span>
                    <span class="text-success"><strong>AED {{ number_format($booking->total_amount, 2) }}</strong></span>
                </div>
            </div>

            <form id="mockPaymentForm" action="{{ route('booking.payment.success') }}" method="GET">
                <input type="hidden" name="session_id" value="mock_session_{{ $booking->id }}">
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                <button type="submit" class="btn btn-pay">
                    <i class="bi bi-shield-check me-2"></i>
                    Complete Payment - AED {{ number_format($booking->total_amount, 2) }}
                </button>
            </form>

            <div class="text-center">
                <a href="{{ route('cars.show', ['id' => $booking->vehicle_id]) }}" class="btn btn-cancel">
                    <i class="bi bi-arrow-left me-1"></i>
                    Cancel & Go Back
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('mockPaymentForm').addEventListener('submit', function(e) {
            e.target.querySelector('button').innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing Payment...';
            e.target.querySelector('button').disabled = true;
        });
    </script>
</body>
</html>
