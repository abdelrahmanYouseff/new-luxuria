<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Booking Invoice</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; }
    .header { text-align: center; margin-bottom: 16px; }
    .box { border: 1px solid #ddd; padding: 12px; margin-bottom: 12px; }
    .row { display: flex; justify-content: space-between; margin: 6px 0; }
    .label { font-weight: bold; }
    .section-title { font-weight: bold; margin-bottom: 8px; }
    .totals { text-align: right; font-size: 14px; font-weight: bold; }
  </style>
</head>
<body>
  <div class="header">
    <h2>Luxuria UAE - Booking Invoice</h2>
  </div>

  <div class="box">
    <div class="section-title">Customer</div>
    <div class="row"><span class="label">Name:</span> <span>{{ $user->name }}</span></div>
    <div class="row"><span class="label">Email:</span> <span>{{ $user->email }}</span></div>
    <div class="row"><span class="label">Invoice #:</span> <span>BK-{{ $booking->id }}</span></div>
    <div class="row"><span class="label">Date:</span> <span>{{ optional($booking->created_at)->format('Y-m-d H:i') }}</span></div>
  </div>

  <div class="box">
    <div class="section-title">Booking</div>
    <div class="row"><span class="label">Vehicle:</span> <span>{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year ?? 'N/A' }})</span></div>
    <div class="row"><span class="label">From:</span> <span>{{ optional($booking->start_date)->format('Y-m-d') }}</span></div>
    <div class="row"><span class="label">To:</span> <span>{{ optional($booking->end_date)->format('Y-m-d') }}</span></div>
    <div class="row"><span class="label">Days:</span> <span>{{ $booking->total_days }}</span></div>
    <div class="row"><span class="label">Rate Type:</span> <span>{{ $booking->formatted_pricing_type }}</span></div>
  </div>

  <div class="box">
    <div class="section-title">Amounts</div>
    <div class="row"><span class="label">Daily Rate:</span> <span>{{ number_format($booking->daily_rate, 2) }} AED</span></div>
    <div class="row"><span class="label">Applied Rate:</span> <span>{{ number_format($booking->applied_rate, 2) }} AED</span></div>
    <div class="totals">Total: {{ number_format($booking->total_amount, 2) }} AED</div>
  </div>

  <p style="text-align:center; margin-top: 20px;">Thank you for booking with Luxuria UAE.</p>
</body>
</html>
