@extends('layouts.blade_app')

@section('title', 'Booking Summary - Luxuria UAE')

@section('content')
<div class="booking-summary-page">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <!-- Header Section -->
                <div class="booking-header">
                    <div class="booking-progress">
                        <div class="progress-step completed">
                            <div class="step-icon"><i class="bi bi-check"></i></div>
                            <span>Vehicle Selection</span>
                        </div>
                        <div class="progress-line completed"></div>
                        <div class="progress-step completed">
                            <div class="step-icon"><i class="bi bi-check"></i></div>
                            <span>Booking Details</span>
                        </div>
                        <div class="progress-line active"></div>
                        <div class="progress-step active">
                            <div class="step-icon"><i class="bi bi-credit-card"></i></div>
                            <span>Payment</span>
                        </div>
                        <div class="progress-line"></div>
                        <div class="progress-step">
                            <div class="step-icon"><i class="bi bi-check-circle"></i></div>
                            <span>Confirmation</span>
                        </div>
                    </div>

                    <div class="booking-title">
                        <h1><i class="bi bi-clipboard-check me-3"></i>Booking Summary</h1>
                        <p>Review your rental details and complete your payment</p>
                    </div>

                    <!-- External Booking Status -->
                    @if(isset($externalBookingResult))
                        <div class="external-booking-status">
                            @if($externalBookingResult['success'])
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <div>
                                        <strong>External Booking Created Successfully!</strong>
                                        <br>
                                        <small>{{ $externalBookingResult['message'] }}</small>
                                        @if(isset($externalBookingResult['external_booking_id']))
                                            <br>
                                            <small><strong>External ID:</strong> {{ $externalBookingResult['external_booking_id'] }}</small>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>
                                        <strong>External Booking Warning</strong>
                                        <br>
                                        <small>{{ $externalBookingResult['message'] }}</small>
                                        <br>
                                        <small>Your local booking will still be processed.</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Booking Information -->
                    @if(isset($booking))
                        <div class="booking-info">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Booking Information</strong>
                                    <br>
                                    <small><strong>Booking ID:</strong> #{{ $booking->id }}</small>
                                    <br>
                                    <small><strong>Status:</strong> <span class="badge {{ $booking->status_badge_class }}">{{ $booking->formatted_status }}</span></small>
                                    @if($booking->external_reservation_id)
                                        <br>
                                        <small><strong>External ID:</strong> {{ $booking->external_reservation_id }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row g-4">
                    <!-- Left Column - Booking Details -->
                    <div class="col-lg-8">
                        <!-- Vehicle Information -->
                        <div class="summary-card vehicle-card">
                            <div class="card-header">
                                <h3><i class="bi bi-car-front me-2"></i>Vehicle Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="vehicle-showcase">
                                    <div class="vehicle-image">
                                        <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->make }} {{ $vehicle->model }}" onerror="this.src='/asset/image.png'">
                                        <div class="vehicle-badge">{{ ucfirst($vehicle->category) }}</div>
                                    </div>
                                                                            <div class="vehicle-details">
                                            <h4>{{ $vehicle->make ?? 'Vehicle' }} {{ $vehicle->model ?? 'Model' }}</h4>
                                            <p class="vehicle-year">{{ $vehicle->year ?? 'Latest Model' }}</p>

                                        <div class="vehicle-specs">
                                            <div class="spec-item">
                                                <i class="bi bi-people"></i>
                                                <span>{{ $vehicle->seats ?? 5 }} Seats</span>
                                            </div>
                                            <div class="spec-item">
                                                <i class="bi bi-door-open"></i>
                                                <span>{{ $vehicle->doors ?? 4 }} Doors</span>
                                            </div>
                                            <div class="spec-item">
                                                <i class="bi bi-gear"></i>
                                                <span>{{ $vehicle->transmission ?? 'Automatic' }}</span>
                                            </div>
                                            <div class="spec-item">
                                                <i class="bi bi-palette"></i>
                                                <span>{{ $vehicle->color ?? 'Premium' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rental Details -->
                        <div class="summary-card rental-card">
                            <div class="card-header">
                                <h3><i class="bi bi-calendar-event me-2"></i>Rental Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="rental-info">
                                    <div class="info-row">
                                        <div class="info-item">
                                            <i class="bi bi-geo-alt"></i>
                                            <div>
                                                <label>Pick-up Location</label>
                                                <span>{{ $bookingData['emirate'] ?? 'Not specified' }}</span>
                                            </div>
                                        </div>
                                        <div class="info-item">
                                            <i class="bi bi-clock"></i>
                                            <div>
                                                <label>Rental Duration</label>
                                                <span>{{ $bookingData['total_days'] ?? 0 }} days</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="date-range">
                                        <div class="date-item start-date">
                                            <i class="bi bi-calendar-plus"></i>
                                            <div>
                                                <label>Start Date</label>
                                                @if(isset($bookingData['start_date']))
                                                <span>{{ \Carbon\Carbon::parse($bookingData['start_date'])->format('D, M j, Y') }}</span>
                                                <small>{{ \Carbon\Carbon::parse($bookingData['start_date'])->format('g:i A') }}</small>
                                                @else
                                                <span>Not specified</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="date-arrow">
                                            <i class="bi bi-arrow-right"></i>
                                        </div>

                                        <div class="date-item end-date">
                                            <i class="bi bi-calendar-minus"></i>
                                            <div>
                                                <label>End Date</label>
                                                @if(isset($bookingData['end_date']))
                                                <span>{{ \Carbon\Carbon::parse($bookingData['end_date'])->format('D, M j, Y') }}</span>
                                                <small>{{ \Carbon\Carbon::parse($bookingData['end_date'])->format('g:i A') }}</small>
                                                @else
                                                <span>Not specified</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if(isset($bookingData['notes']) && $bookingData['notes'])
                                    <div class="special-requests">
                                        <i class="bi bi-chat-square-text"></i>
                                        <div>
                                            <label>Special Requests</label>
                                            <span>{{ $bookingData['notes'] }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="summary-card customer-card">
                            <div class="card-header">
                                <h3><i class="bi bi-person-circle me-2"></i>Customer Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="customer-info">
                                    <div class="customer-avatar">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div class="customer-details">
                                        <h4>{{ Auth::user()->name ?? 'User' }}</h4>
                                        <p><i class="bi bi-envelope"></i>{{ Auth::user()->email ?? 'No email' }}</p>
                                        @if(Auth::user()->emirate)
                                        <p><i class="bi bi-geo-alt"></i>{{ Auth::user()->emirate }}</p>
                                        @endif
                                        @if(Auth::user()->address)
                                        <p><i class="bi bi-house"></i>{{ Auth::user()->address }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <div class="summary-card payment-card">
                            <div class="card-header">
                                <h3><i class="bi bi-credit-card me-2"></i>Select Payment Method</h3>
                            </div>
                            <div class="card-body">
                                <div class="payment-methods">
                                    <div class="payment-option active" data-method="stripe">
                                        <div class="payment-icon">
                                            <i class="bi bi-credit-card-2-front"></i>
                                        </div>
                                        <div class="payment-details">
                                            <h5>Credit/Debit Card</h5>
                                            <p>Secure payment with Stripe</p>
                                            <div class="payment-badges">
                                                <img src="https://cdn.worldvectorlogo.com/logos/visa-10.svg" alt="Visa" class="payment-badge">
                                                <img src="https://cdn.worldvectorlogo.com/logos/mastercard-6.svg" alt="Mastercard" class="payment-badge">
                                                <img src="https://cdn.worldvectorlogo.com/logos/american-express-3.svg" alt="American Express" class="payment-badge">
                                            </div>
                                        </div>
                                        <div class="payment-check">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    </div>

                                    <div class="payment-option" data-method="bank">
                                        <div class="payment-icon">
                                            <i class="bi bi-bank"></i>
                                        </div>
                                        <div class="payment-details">
                                            <h5>Bank Transfer</h5>
                                            <p>Direct bank transfer payment</p>
                                            <small class="text-muted">Processing may take 1-2 business days</small>
                                        </div>
                                        <div class="payment-check">
                                            <i class="bi bi-circle"></i>
                                        </div>
                                    </div>

                                    <div class="payment-option" data-method="wallet">
                                        <div class="payment-icon">
                                            <i class="bi bi-wallet2"></i>
                                        </div>
                                        <div class="payment-details">
                                            <h5>Digital Wallet</h5>
                                            <p>Apple Pay, Google Pay, Samsung Pay</p>
                                            <small class="text-muted">Quick and secure</small>
                                        </div>
                                        <div class="payment-check">
                                            <i class="bi bi-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Price Summary -->
                    <div class="col-lg-4">
                        <div class="price-summary-card sticky-top">
                            <div class="card-header">
                                <h3><i class="bi bi-calculator"></i>Price Summary</h3>
                            </div>
                            <div class="card-body">
                                <div class="price-breakdown">
                                                                        <div class="price-item">
                                        <span>Base Rate ({{ $bookingData['pricing_type'] ?? 'daily' }})</span>
                                        <span>AED {{ number_format($bookingData['applied_rate'] ?? 0, 2) }}</span>
                                    </div>

                                    <div class="price-item">
                                        <span>Duration</span>
                                        <span>{{ $bookingData['total_days'] ?? 0 }} days</span>
                                    </div>

                                    <div class="price-item subtotal">
                                        <span>Subtotal</span>
                                        <span><b>AED {{ number_format($bookingData['total_amount'] ?? 0, 2) }}</b></span>
                                    </div>

                                    <div class="price-item">
                                        <span>Tax & Fees</span>
                                        <span>AED 0.00</span>
                                    </div>

                                    <div class="price-item discount" style="display: none;">
                                        <span>Discount</span>
                                        <span class="text-success">-AED 0.00</span>
                                    </div>

                                    <hr>

                                    <div class="price-item total">
                                        <span>Total Amount</span>
                                        <span>AED {{ number_format($bookingData['total_amount'] ?? 0, 2) }}</span>
                                    </div>
                                </div>

                                <div class="price-info">
                                    <div class="info-badge"><i class="bi bi-shield-check"></i> Secure Payment</div>
                                    <div class="info-badge"><i class="bi bi-arrow-clockwise"></i> Free Cancellation</div>
                                    <div class="info-badge"><i class="bi bi-telephone"></i> 24/7 Support</div>
                                </div>

                                <form id="paymentForm" action="{{ route('booking.confirm') }}" method="POST">
                                    @csrf
                                    @if(isset($booking))
                                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                    @else
                                        <input type="hidden" name="booking_data" value="{{ json_encode($bookingData) }}">
                                    @endif
                                    <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="stripe">

                                    <button type="submit" class="btn btn-proceed" id="submitButton">
                                        <span class="btn-icon"><i class="bi bi-lock-fill"></i></span>
                                        <span class="btn-label">Proceed to Payment</span>
                                        <span class="btn-amount">AED {{ number_format($bookingData['total_amount'] ?? 0, 2) }}</span>
                                    </button>
                                </form>

                                <div class="payment-security">
                                    <i class="bi bi-shield-lock"></i> Your payment information is encrypted and secure
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.booking-summary-page {
    background: #fff;
    min-height: 100vh;
    padding: 120px 0 80px;
    color: #222;
}

/* Header Section */
.booking-header {
    text-align: center;
    margin-bottom: 60px;
}

.booking-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 40px;
    flex-wrap: wrap;
    gap: 10px;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    min-width: 120px;
}

.step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    background: rgba(191, 161, 51, 0.08);
    border: 2px solid #f3e7c1;
    color: #bfa133;
    transition: all 0.3s ease;
}

.progress-step.completed .step-icon {
    background: linear-gradient(135deg, #bfa133, #d4b846);
    border-color: #bfa133;
    color: #fff;
}

.progress-step.active .step-icon {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    border-color: #0ea5e9;
    color: #fff;
    animation: pulse 2s infinite;
}

.progress-step span {
    font-size: 0.9rem;
    font-weight: 500;
    color: #bfa133;
}

.progress-step.completed span,
.progress-step.active span {
    color: #222;
    font-weight: 600;
}

.progress-line {
    width: 80px;
    height: 2px;
    background: #f3e7c1;
    margin: 0 10px;
}

.progress-line.completed {
    background: linear-gradient(90deg, #bfa133, #d4b846);
}

.progress-line.active {
    background: linear-gradient(90deg, #bfa133, #0ea5e9);
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.booking-title h1 {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    font-weight: 700;
    color: #bfa133;
    margin-bottom: 10px;
}

.booking-title p {
    font-size: 1.1rem;
    color: #666;
    margin: 0;
}

/* External Booking Status */
.external-booking-status {
    margin-bottom: 30px;
}

.external-booking-status .alert {
    border-radius: 15px;
    border: none;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.external-booking-status .alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

.external-booking-status .alert-warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
}

.external-booking-status .alert i {
    font-size: 1.5rem;
}

.external-booking-status .alert strong {
    font-weight: 700;
}

.external-booking-status .alert small {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Summary Cards */
.summary-card {
    background: #fff;
    border: 1px solid #f3e7c1;
    border-radius: 20px;
    margin-bottom: 30px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(191, 161, 51, 0.07);
}

.summary-card .card-header {
    background: linear-gradient(135deg, #fffbe6 0%, #f7ecd0 100%);
    border-bottom: 1px solid #f3e7c1;
    padding: 25px 30px;
}

.summary-card .card-header h3 {
    margin: 0;
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem;
    font-weight: 700;
    color: #bfa133;
    display: flex;
    align-items: center;
}

.summary-card .card-body {
    padding: 30px;
}

/* Vehicle Card */
.vehicle-showcase {
    display: flex;
    gap: 30px;
    align-items: center;
}

.vehicle-image {
    position: relative;
    min-width: 200px;
}

.vehicle-image img {
    width: 200px;
    height: 140px;
    object-fit: cover;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(191, 161, 51, 0.08);
}

.vehicle-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #bfa133, #d4b846);
    color: #fff;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.vehicle-details h4 {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 700;
    color: #222;
    margin-bottom: 5px;
}

.vehicle-year {
    color: #bfa133;
    font-size: 1.1rem;
    margin-bottom: 20px;
}

.vehicle-specs {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.spec-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #555;
}

.spec-item i {
    color: #bfa133;
    font-size: 1.1rem;
}

/* Rental Details */
.rental-info .info-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    margin-bottom: 30px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.info-item i {
    color: #bfa133;
    font-size: 1.3rem;
    min-width: 20px;
}

.info-item label {
    display: block;
    color: #888;
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.info-item span {
    color: #222;
    font-weight: 600;
    font-size: 1.1rem;
}

.date-range {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fffbe6;
    border: 1px solid #f3e7c1;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
}

.date-item {
    display: flex;
    align-items: center;
    gap: 15px;
    text-align: left;
}

.date-item i {
    color: #bfa133;
    font-size: 1.5rem;
}

.date-item label {
    display: block;
    color: #888;
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.date-item span {
    color: #222;
    font-weight: 600;
    font-size: 1.1rem;
}

.date-item small {
    color: #bfa133;
    font-size: 0.9rem;
    margin-top: 3px;
    display: block;
}

.date-arrow {
    color: #bfa133;
    font-size: 1.5rem;
    margin: 0 20px;
}

.special-requests {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    background: #e3f2fd;
    border: 1px solid #bfa13333;
    border-radius: 10px;
    padding: 20px;
}

.special-requests i {
    color: #0ea5e9;
    font-size: 1.2rem;
    margin-top: 3px;
}

/* Customer Info */
.customer-info {
    display: flex;
    align-items: center;
    gap: 25px;
}

.customer-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #bfa133, #d4b846);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #fff;
}

.customer-details h4 {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    color: #222;
    margin-bottom: 10px;
}

.customer-details p {
    color: #555;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.customer-details i {
    color: #bfa133;
    width: 16px;
}

/* Payment Methods */
.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.payment-option {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    border: 2px solid #f3e7c1;
    border-radius: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
}

.payment-option:hover {
    border-color: #bfa133;
    background: #fffbe6;
}

.payment-option.active {
    border-color: #bfa133;
    background: #fffbe6;
}

.payment-icon {
    width: 50px;
    height: 50px;
    background: #f3e7c1;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: #bfa133;
}

.payment-details {
    flex: 1;
}

.payment-details h5 {
    color: #222;
    font-weight: 600;
    margin-bottom: 5px;
}

.payment-details p {
    color: #666;
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.payment-badges {
    display: flex;
    gap: 8px;
}

.payment-badge {
    height: 24px;
    width: auto;
    filter: brightness(0.8);
}

.payment-check {
    font-size: 1.3rem;
    color: #bfa133;
}

.payment-option:not(.active) .payment-check i {
    color: #f3e7c1;
}

/* Price Summary */
.price-summary-card {
    background: #fff;
    border: none;
    border-radius: 24px;
    box-shadow: 0 8px 32px rgba(191,161,51,0.10), 0 1.5px 0 #f3e7c1;
    padding: 0 0 24px 0;
    margin-bottom: 32px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.price-summary-card .card-header {
    background: linear-gradient(90deg, #fffbe6 60%, #fff 100%);
    border-bottom: none;
    padding: 32px 32px 16px 32px;
}
.price-summary-card .card-header h3 {
    font-size: 1.6rem;
    font-family: 'Playfair Display', serif;
    color: #222;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}
.price-breakdown {
    padding: 0 32px;
    margin-bottom: 18px;
}
.price-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 0 0 0;
    font-size: 1.08rem;
    color: #444;
    font-weight: 400;
}
.price-item:not(:last-child) {
    border-bottom: 1px solid #f3e7c1;
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
}
.price-item.subtotal {
    font-weight: 600;
    font-size: 1.13rem;
    color: #222;
    background: #fffbe6;
    border-radius: 8px;
    margin: 18px 0 0 0;
    padding: 16px 0 0 0;
    border: none;
}
.price-item.total {
    font-size: 1.25rem;
    font-weight: 800;
    color: #bfa133;
    background: none;
    border: none;
    margin-top: 18px;
    padding-top: 18px;
}
.price-info {
    padding: 0 32px;
    margin: 18px 0 0 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.info-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #bfa133;
    font-size: 1.04rem;
    font-weight: 500;
}
.info-badge i {
    font-size: 1.2rem;
    color: #bfa133;
}
.btn-proceed {
    width: calc(100% - 64px);
    margin: 24px 32px 0 32px;
    background: linear-gradient(90deg, #bfa133 60%, #d4b846 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 13px 0 13px 0;
    font-size: 1.08rem;
    font-weight: 700;
    letter-spacing: 0.2px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    box-shadow: 0 2px 10px rgba(191,161,51,0.10);
    transition: background 0.2s, box-shadow 0.2s;
    min-height: 48px;
}
.btn-proceed:hover {
    background: linear-gradient(90deg, #d4b846 60%, #bfa133 100%);
    box-shadow: 0 4px 18px rgba(191,161,51,0.18);
}
.btn-proceed .btn-icon {
    font-size: 1.15rem;
    margin-left: 18px;
    color: #222;
    flex-shrink: 0;
}
.btn-proceed .btn-label {
    flex: 1;
    text-align: left;
    font-weight: 700;
    font-size: 1.08rem;
    color: #222;
    letter-spacing: 0.1px;
}
.btn-proceed .btn-amount {
    font-size: 1.08rem;
    font-weight: 900;
    color: #fff;
    margin-right: 18px;
    text-align: right;
    min-width: 110px;
}
.payment-security {
    text-align: center;
    margin-top: 18px;
    color: #bfa133;
    font-size: 1.01rem;
    background: #fffbe6;
    border-radius: 0 0 18px 18px;
    padding: 10px 0 0 0;
    border-top: 1px solid #f3e7c1;
}
.payment-security i {
    color: #bfa133;
    margin-right: 5px;
}
@media (max-width: 768px) {
    .price-summary-card, .price-breakdown, .price-info {
        padding: 0 10px !important;
    }
    .btn-proceed {
        width: 100%;
        margin: 18px 0 0 0;
        font-size: 1rem;
        min-height: 44px;
    }
    .btn-proceed .btn-label {
        font-size: 1rem;
    }
    .btn-proceed .btn-amount {
        font-size: 1rem;
        min-width: 90px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing booking form...');

    // Payment method selection
    const paymentOptions = document.querySelectorAll('.payment-option');
    const selectedPaymentMethod = document.getElementById('selectedPaymentMethod');

    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            paymentOptions.forEach(opt => opt.classList.remove('active'));

            // Add active class to selected option
            this.classList.add('active');

            // Update hidden input
            selectedPaymentMethod.value = this.dataset.method;

            // Update icons
            paymentOptions.forEach(opt => {
                const icon = opt.querySelector('.payment-check i');
                icon.className = 'bi bi-circle';
            });

            const selectedIcon = this.querySelector('.payment-check i');
            selectedIcon.className = 'bi bi-check-circle-fill';
        });
    });

    // Form submission - SIMPLIFIED VERSION
    const paymentForm = document.getElementById('paymentForm');
    const submitButton = document.getElementById('submitButton');

    console.log('Form found:', paymentForm);
    console.log('Submit button found:', submitButton);
    console.log('Form action:', paymentForm.action);
    console.log('Form method:', paymentForm.method);

    // Remove any existing event listeners and add a simple one
    paymentForm.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');

        // Show loading state
        submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';
        submitButton.disabled = true;

        console.log('Form is submitting to:', this.action);
        console.log('Form data:', {
            booking_data: this.querySelector('input[name="booking_data"]').value,
            payment_method: this.querySelector('input[name="payment_method"]').value,
            _token: this.querySelector('input[name="_token"]').value
        });

        // Let the form submit naturally - NO preventDefault()
    });

    // Smooth scrolling for mobile
    if (window.innerWidth <= 768) {
        const priceCard = document.querySelector('.price-summary-card');
        if (priceCard) {
            priceCard.style.position = 'static';
        }
    }
});
</script>
@endsection
