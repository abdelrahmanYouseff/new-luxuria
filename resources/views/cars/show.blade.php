@extends('layouts.blade_app')

@section('title', $vehicle->make . ' ' . $vehicle->model . ' - Car Details')

@section('content')
<div class="car-details-section px-0 px-md-5 mb-5 mt-4 responsive-car-details">
    <div class="row justify-content-center align-items-center gx-5 gy-4">
        <div class="col-12 col-lg-6 text-center mb-4 mb-lg-0">
            <div class="car-hero-img-wrap2 d-flex align-items-center justify-content-center h-100">
                @if($vehicle->image)
                    <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->make }} {{ $vehicle->model }}" class="car-hero-img2" onerror="this.src='{{ asset('asset/image.png') }}'">
                @else
                    <img src="{{ asset('asset/image.png') }}" alt="{{ $vehicle->make }} {{ $vehicle->model }}" class="car-hero-img2">
                @endif
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <h1 class="lux-heading mb-4" style="font-size:2.8rem; color:#111;">{{ $vehicle->make }} {{ $vehicle->model }}</h1>
            <div class="d-flex flex-wrap gap-3 mb-4">
                <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill">{{ $vehicle->seats ?? 5 }} Seats</span>
                <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill">{{ $vehicle->doors ?? 4 }} Doors</span>
                <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">{{ $vehicle->deposit ?? 'No Deposit' }}</span>
                <span class="badge bg-info fs-6 px-3 py-2 rounded-pill">{{ ucfirst($vehicle->category ?? 'Standard') }}</span>
                <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill">{{ $vehicle->transmission ?? 'Automatic' }}</span>
                <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill">{{ $vehicle->color ?? 'N/A' }}</span>
                <span class="badge bg-warning fs-6 px-3 py-2 rounded-pill">{{ ucfirst($vehicle->status ?? 'Available') }}</span>
            </div>
            <p class="mb-4" style="font-size:1.15rem; color:#444;">Experience the ultimate in luxury and performance with the {{ $vehicle->make }} {{ $vehicle->model }}. Perfect for business, leisure, and special occasions in the UAE. {{ $vehicle->description ?? '' }}</p>
            <!-- Compact Luxury Pricing Section -->
            <div class="lux-pricing-compact my-4">
                <div class="row g-3 justify-content-center align-items-end">
                    <!-- Daily -->
                    <div class="col-4 col-sm-4 col-12 col-md-4">
                        <div class="lux-pricing-card-compact text-center p-3 h-100">
                            <div class="lux-pricing-icon-compact mb-1">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="lux-pricing-label-compact mb-1">Daily</div>
                            <div class="lux-pricing-amount-compact">
                                <span class="lux-pricing-currency-compact">AED</span>
                                <span class="lux-pricing-value-compact">{{ number_format($vehicle->daily_rate ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Weekly -->
                    <div class="col-4 col-sm-4 col-12 col-md-4">
                        <div class="lux-pricing-card-compact text-center p-3 h-100">
                            <div class="lux-pricing-icon-compact mb-1">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                            <div class="lux-pricing-label-compact mb-1">Weekly</div>
                            <div class="lux-pricing-amount-compact">
                                <span class="lux-pricing-currency-compact">AED</span>
                                <span class="lux-pricing-value-compact">{{ number_format($vehicle->weekly_rate ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Monthly -->
                    <div class="col-4 col-sm-4 col-12 col-md-4">
                        <div class="lux-pricing-card-compact text-center p-3 h-100">
                            <div class="lux-pricing-icon-compact mb-1">
                                <i class="bi bi-calendar2-month"></i>
                            </div>
                            <div class="lux-pricing-label-compact mb-1">Monthly</div>
                            <div class="lux-pricing-amount-compact">
                                <span class="lux-pricing-currency-compact">AED</span>
                                <span class="lux-pricing-value-compact">{{ number_format($vehicle->monthly_rate ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    @auth
                        <button id="bookNowBtn" class="btn lux-btn-gold-compact px-4 py-2 shadow-sm" data-vehicle-id="{{ $vehicle->id }}" data-vehicle-status="{{ $vehicle->status }}">
                            Book Now
                        </button>
                    @else
                        <a href="{{ route('login', ['redirect' => request()->url()]) }}" class="btn lux-btn-gold-compact px-4 py-2 shadow-sm">Login to Book</a>
                    @endauth
                </div>
            </div>
            <!-- End Compact Luxury Pricing Section -->
            <div class="d-flex align-items-center gap-2 mt-2">
                <a href="https://wa.me/971501234567?text=Hi, I'm interested in booking the {{ $vehicle->make }} {{ $vehicle->model }} for {{ number_format($vehicle->daily_rate ?? 0) }} AED/day" target="_blank" class="lux-whatsapp-icon"><img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" width="38" height="38"></a>
                <span style="color:#444; font-size:1.1rem;">Chat with us for instant booking</span>
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
@auth
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content luxury-modal">
            <div class="modal-header luxury-modal-header">
                <div class="luxury-modal-title">
                    <div class="luxury-modal-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" id="bookingModalLabel">Book Your Luxury Vehicle</h5>
                        <p class="mb-0 luxury-modal-subtitle">{{ $vehicle->make }} {{ $vehicle->model }} {{ $vehicle->year ?? '' }}</p>
                        <small class="luxury-modal-details">{{ $vehicle->category ?? 'Luxury' }} • {{ $vehicle->transmission ?? 'Automatic' }} • {{ $vehicle->seats ?? 5 }} Seats</small>
                    </div>
                </div>
                <button type="button" class="btn-close luxury-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body luxury-modal-body">
                <!-- Smart Pricing Info -->
                <div class="luxury-pricing-info">
                    <div class="luxury-pricing-header">
                        <div class="luxury-pricing-icon">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <h6 class="mb-0">Smart Pricing System</h6>
                    </div>
                    <div class="luxury-pricing-grid">
                        <div class="luxury-pricing-item">
                            <div class="luxury-pricing-duration">1-7 days</div>
                            <div class="luxury-pricing-rate">Daily Rate</div>
                            <div class="luxury-pricing-amount">AED {{ number_format($vehicle->daily_rate ?? 0) }}/day</div>
                        </div>
                        <div class="luxury-pricing-item">
                            <div class="luxury-pricing-duration">8-27 days</div>
                            <div class="luxury-pricing-rate">Weekly Rate</div>
                            <div class="luxury-pricing-amount">AED {{ number_format(($vehicle->weekly_rate ?? 0) / 7) }}/day</div>
                            <div class="luxury-pricing-savings">Save {{ round((1 - (($vehicle->weekly_rate ?? 0) / 7) / ($vehicle->daily_rate ?? 1)) * 100) }}%</div>
                        </div>
                        <div class="luxury-pricing-item">
                            <div class="luxury-pricing-duration">28+ days</div>
                            <div class="luxury-pricing-rate">Monthly Rate</div>
                            <div class="luxury-pricing-amount">AED {{ number_format(round(($vehicle->monthly_rate ?? 0) / 30)) }}/day</div>
                            <div class="luxury-pricing-savings">Save {{ round((1 - (round(($vehicle->monthly_rate ?? 0) / 30)) / ($vehicle->daily_rate ?? 1)) * 100) }}%</div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Status Alert -->
                <div id="statusAlert" class="alert d-none" role="alert"></div>

                <form id="bookingForm">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                    <div class="row g-3">
                        <!-- Emirate Selection -->
                        <div class="col-12">
                            <label for="emirate" class="luxury-form-label">
                                <i class="bi bi-geo-alt"></i>Pick-up Emirate
                            </label>
                            <select class="form-select luxury-form-select" id="emirate" name="emirate" required>
                                <option value="">Select an emirate</option>
                                <option value="Abu Dhabi">Abu Dhabi</option>
                                <option value="Dubai">Dubai</option>
                                <option value="Sharjah">Sharjah</option>
                                <option value="Ajman">Ajman</option>
                                <option value="Umm Al Quwain">Umm Al Quwain</option>
                                <option value="Ras Al Khaimah">Ras Al Khaimah</option>
                                <option value="Fujairah">Fujairah</option>
                            </select>
                        </div>

                        <!-- Date Selection -->
                        <div class="col-md-6">
                            <label for="start_date" class="luxury-form-label">
                                <i class="bi bi-calendar-event"></i>Start Date
                            </label>
                            <input type="date" class="form-control luxury-form-input" id="start_date" name="start_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="end_date" class="luxury-form-label">
                                <i class="bi bi-calendar-x"></i>End Date
                            </label>
                            <input type="date" class="form-control luxury-form-input" id="end_date" name="end_date" required min="{{ date('Y-m-d', strtotime('+2 days')) }}">
                        </div>

                        <!-- Booking Summary -->
                        <div class="col-12">
                            <div class="luxury-booking-summary">
                                <div class="luxury-summary-header">
                                    <div class="luxury-summary-icon">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <h6 class="mb-0">Booking Summary</h6>
                                </div>
                                <div class="luxury-summary-grid">
                                    <div class="luxury-summary-item">
                                        <div class="luxury-summary-label">Smart Pricing</div>
                                        <div class="luxury-summary-rates">
                                            <div class="luxury-rate-item">Daily: AED {{ number_format($vehicle->daily_rate ?? 0) }}</div>
                                            <div class="luxury-rate-item">Weekly: AED {{ number_format(($vehicle->weekly_rate ?? 0) / 7) }}/day</div>
                                            <div class="luxury-rate-item">Monthly: AED {{ number_format(round(($vehicle->monthly_rate ?? 0) / 30)) }}/day</div>
                                        </div>
                                    </div>
                                    <div class="luxury-summary-item">
                                        <div class="luxury-summary-label">Total Days</div>
                                        <div class="luxury-summary-value" id="totalDays">-</div>
                                    </div>
                                    <div class="luxury-summary-item">
                                        <div class="luxury-summary-label">Total Amount</div>
                                        <div class="luxury-summary-amount" id="totalAmount">AED -</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="luxury-form-label">
                                <i class="bi bi-chat-text"></i>Special Requests (Optional)
                            </label>
                            <textarea class="form-control luxury-form-textarea" id="notes" name="notes" rows="4" placeholder="Any special requests or notes..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer luxury-modal-footer">
                <button type="button" class="btn luxury-btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i>Cancel
                </button>
                <button type="button" id="submitBooking" class="btn luxury-btn-confirm">
                    <i class="bi bi-check-circle"></i>Confirm Booking
                </button>
            </div>
        </div>
    </div>
</div>
@endauth

<!-- Alternative Vehicles Modal -->
@auth
<div class="modal fade" id="alternativeVehiclesModal" tabindex="-1" aria-labelledby="alternativeVehiclesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content luxury-modal">
            <div class="modal-header luxury-modal-header">
                <div class="luxury-modal-title">
                    <div class="luxury-modal-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="bi bi-calendar-x-fill"></i>
                    </div>
                                         <div>
                        <h5 class="mb-0" id="alternativeVehiclesModalLabel">Vehicle Currently Unavailable</h5>
                        <p class="mb-0 luxury-modal-subtitle" id="originalVehicleName">{{ $vehicle->make }} {{ $vehicle->model }} {{ $vehicle->year ?? '' }}</p>
                        <small class="luxury-modal-details">We've found similar luxury vehicles for you</small>
                    </div>
                </div>
                <button type="button" class="btn-close luxury-btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body luxury-modal-body">
                <!-- Unavailable Vehicle Info -->
                <div class="luxury-unavailable-info">
                    <div class="luxury-unavailable-header">
                        <div class="luxury-unavailable-icon">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Sorry, this vehicle is not available right now</h6>
                            <p class="mb-0 text-muted">But don't worry! We have found similar luxury vehicles that might interest you.</p>
                        </div>
                    </div>
                </div>

                <!-- Alternative Vehicles Grid -->
                <div class="luxury-alternatives-section">
                    <h6 class="luxury-alternatives-title">
                        <i class="bi bi-stars me-2"></i>Recommended Alternatives
                    </h6>
                    <div class="luxury-alternatives-grid" id="alternativesGrid">
                        <!-- Dynamic content will be inserted here -->
                    </div>
                </div>

                <!-- Loading State -->
                <div class="luxury-alternatives-loading" id="alternativesLoading">
                    <div class="luxury-loading-spinner"></div>
                    <p>Finding the best alternatives for you...</p>
                </div>

                <!-- No Alternatives State -->
                <div class="luxury-no-alternatives d-none" id="noAlternatives">
                    <div class="luxury-no-alternatives-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h6>No alternatives found</h6>
                    <p>Please contact us directly for assistance.</p>
                    <a href="https://wa.me/971501234567" target="_blank" class="btn luxury-btn-confirm">
                        <i class="bi bi-whatsapp"></i>Contact via WhatsApp
                    </a>
                </div>
            </div>
            <div class="modal-footer luxury-modal-footer">
                <button type="button" class="btn luxury-btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left"></i>Go Back
                </button>
                <a href="https://wa.me/971501234567" target="_blank" class="btn luxury-btn-secondary">
                    <i class="bi bi-headset"></i>Need Help?
                </a>
            </div>
        </div>
    </div>
</div>
@endauth

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
.car-details-section {
    max-width: 1400px;
    margin: 0 auto;
}
.car-hero-img-wrap2 {
    background: none;
    border-radius: 0;
    min-height: 0;
    padding: 0;
    box-shadow: none;
}
.car-hero-img2 {
    width: 100%;
    max-width: 1000px;
    max-height: 600px;
    object-fit: contain;
    border-radius: 2.2rem;
    background: none;
    box-shadow: 0 4px 24px 0 rgba(191,161,51,0.08);
}
.lux-pricing-compact {
    max-width: 600px;
    margin: 0 auto;
}
.lux-pricing-card-compact {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 12px 0 rgba(191,161,51,0.08);
    border: 1px solid #f7e7c1;
    min-width: 0;
    min-height: 120px;
    transition: box-shadow 0.18s, transform 0.18s;
}
.lux-pricing-card-compact:hover {
    box-shadow: 0 6px 18px 0 rgba(191,161,51,0.16), 0 0 0 2px #bfa13333;
    transform: translateY(-2px) scale(1.03);
    z-index: 2;
}
.lux-pricing-icon-compact {
    font-size: 1.5rem;
    color: #bfa133;
    margin-bottom: 0.2rem;
}
.lux-pricing-label-compact {
    color: #bfa133;
    font-size: 1rem;
    font-weight: 700;
    font-family: 'Playfair Display', serif;
    letter-spacing: 0.5px;
}
.lux-pricing-amount-compact {
    font-size: 1.25rem;
    font-weight: 900;
    color: #111;
    font-family: 'Playfair Display', serif;
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 0.2rem;
}
.lux-pricing-currency-compact {
    font-size: 0.9rem;
    color: #bfa133;
    font-weight: 700;
    margin-right: 0.1rem;
}
.lux-pricing-value-compact {
    font-size: 1.3rem;
    font-weight: 900;
    color: #111;
}
.lux-btn-gold-compact {
    background: #bfa133;
    color: #fff !important;
    border-radius: 1.5rem;
    font-weight: 700;
    font-size: 1.05rem;
    border: none;
    letter-spacing: 1px;
    box-shadow: 0 1px 6px 0 #bfa13322;
    transition: background 0.18s, box-shadow 0.18s;
}
.lux-btn-gold-compact:hover {
    background: #a88c2c;
    color: #fff !important;
    box-shadow: 0 2px 12px 0 #bfa13344;
}
/* إضافة margin-top أكبر أسفل الهيدر */
.responsive-car-details {
    margin-top: 120px !important;
}
@media (max-width: 991px) {
    .responsive-car-details {
        margin-top: 90px !important;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .car-hero-img2 {
        max-width: 100%;
        max-height: 220px;
    }
    .car-hero-img-wrap2 {
        min-height: 140px;
        padding: 1.2rem 0.5rem;
    }
}
/* ===============================
   LUXURY MODAL STYLES
   =============================== */

/* Modal Base */
.luxury-modal {
    border: none;
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(191, 161, 51, 0.15), 0 0 0 1px rgba(191, 161, 51, 0.1);
    overflow: hidden;
    backdrop-filter: blur(10px);
    animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes modalSlideIn {
    from {
        transform: translateY(-50px) scale(0.9);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

/* Modal Backdrop Enhancement */
.modal-backdrop {
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
}

/* Modal Header */
.luxury-modal-header {
    background: linear-gradient(135deg, #bfa133 0%, #d4b852 100%);
    border-bottom: none;
    padding: 1.5rem 2rem;
}

.luxury-modal-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
}

.luxury-modal-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.luxury-modal-title h5 {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    font-size: 1.4rem;
    color: white;
    margin: 0;
}

.luxury-modal-subtitle {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 400;
}

.luxury-modal-details {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 300;
    display: block;
    margin-top: 0.25rem;
}

.luxury-btn-close {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    border: none;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.luxury-btn-close:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    transform: rotate(90deg);
}

/* Modal Body */
.luxury-modal-body {
    padding: 2rem;
    background: linear-gradient(135deg, #fefefe 0%, #f8f9fa 100%);
}

/* Pricing Info Section */
.luxury-pricing-info {
    background: linear-gradient(135deg, #fff8e7 0%, #faf4e1 100%);
    border: 2px solid #bfa13330;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(191, 161, 51, 0.1);
}

.luxury-pricing-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.luxury-pricing-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #bfa133, #d4b852);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.luxury-pricing-header h6 {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    color: #8b6914;
    font-size: 1.1rem;
}

.luxury-pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.luxury-pricing-item {
    background: white;
    border: 1px solid #bfa13320;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.luxury-pricing-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #bfa133, #d4b852);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.luxury-pricing-item:hover::before {
    transform: scaleX(1);
}

.luxury-pricing-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(191, 161, 51, 0.15);
    border-color: #bfa13350;
}

.luxury-pricing-duration {
    font-weight: 700;
    color: #bfa133;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.luxury-pricing-rate {
    color: #666;
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
}

.luxury-pricing-amount {
    font-weight: 900;
    color: #333;
    font-size: 1rem;
    font-family: 'Playfair Display', serif;
}

.luxury-pricing-savings {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    margin-top: 0.5rem;
    display: inline-block;
}

/* Form Styles */
.luxury-form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.luxury-form-label i {
    color: #bfa133;
    font-size: 1.1rem;
}

.luxury-form-select,
.luxury-form-input,
.luxury-form-textarea {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
}

.luxury-form-select:focus,
.luxury-form-input:focus,
.luxury-form-textarea:focus {
    border-color: #bfa133;
    box-shadow: 0 0 0 3px rgba(191, 161, 51, 0.1);
    outline: none;
}

.luxury-form-textarea {
    resize: vertical;
    min-height: 100px;
}

/* Booking Summary */
.luxury-booking-summary {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 2px solid #0ea5e920;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.luxury-summary-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.luxury-summary-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.luxury-summary-header h6 {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    color: #075985;
    font-size: 1.1rem;
}

.luxury-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
}

.luxury-summary-item {
    text-align: center;
}

.luxury-summary-label {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.luxury-summary-value {
    font-weight: 700;
    font-size: 1.2rem;
    color: #334155;
    font-family: 'Playfair Display', serif;
}

.luxury-summary-amount {
    font-weight: 900;
    font-size: 1.4rem;
    color: #059669;
    font-family: 'Playfair Display', serif;
}

.luxury-summary-rates {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.luxury-rate-item {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
}

/* Modal Footer */
.luxury-modal-footer {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-top: 1px solid #dee2e6;
    padding: 1.5rem 2rem;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.luxury-btn-cancel,
.luxury-btn-confirm {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    border: none;
    position: relative;
    overflow: hidden;
}

.luxury-btn-cancel {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: white;
}

.luxury-btn-cancel:hover {
    background: linear-gradient(135deg, #5a6268, #343a40);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    color: white;
}

.luxury-btn-confirm {
    background: linear-gradient(135deg, #bfa133, #d4b852);
    color: white;
    font-family: 'Playfair Display', serif;
}

.luxury-btn-confirm:hover {
    background: linear-gradient(135deg, #a88c2c, #c2a347);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(191, 161, 51, 0.4);
    color: white;
}

.luxury-btn-confirm:disabled {
    background: linear-gradient(135deg, #9ca3af, #6b7280);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Loading Animation for Submit Button */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.luxury-btn-confirm:disabled .bi-hourglass-split {
    animation: spin 1s linear infinite;
}

/* ===============================
   ALTERNATIVE VEHICLES MODAL STYLES
   =============================== */

/* Unavailable Info Section */
.luxury-unavailable-info {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 2px solid #fca5a520;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.luxury-unavailable-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.luxury-unavailable-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.luxury-unavailable-header h6 {
    color: #991b1b;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    margin: 0;
}

.luxury-unavailable-header p {
    color: #7f1d1d;
    font-size: 0.95rem;
}

/* Alternatives Section */
.luxury-alternatives-section {
    margin-bottom: 2rem;
}

.luxury-alternatives-title {
    color: #bfa133;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.luxury-alternatives-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
}

.luxury-alternative-card {
    background: white;
    border: 2px solid #bfa13320;
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(191, 161, 51, 0.1);
}

.luxury-alternative-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #bfa133, #d4b852);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.luxury-alternative-card:hover::before {
    transform: scaleX(1);
}

.luxury-alternative-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(191, 161, 51, 0.2);
    border-color: #bfa13350;
}

.luxury-alternative-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    gap: 0.75rem;
}

.luxury-alternative-info h6 {
    color: #333;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    font-size: 0.95rem;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}

.luxury-alternative-info p {
    color: #666;
    font-size: 0.9rem;
    margin: 0.25rem 0 0 0;
}

.luxury-alternative-price {
    text-align: right;
}

.luxury-alternative-amount {
    color: #bfa133;
    font-family: 'Playfair Display', serif;
    font-weight: 900;
    font-size: 1.2rem;
}

.luxury-alternative-period {
    color: #666;
    font-size: 0.8rem;
    display: block;
}

.luxury-price-difference {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    margin-top: 0.5rem;
    display: inline-block;
    white-space: nowrap;
    min-width: fit-content;
}

.luxury-price-difference.cheaper {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.luxury-price-difference.more-expensive {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.luxury-price-difference.same-price {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
}

.luxury-alternative-specs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin: 1rem 0;
}

.luxury-spec-badge {
    background: #f3f4f6;
    color: #374151;
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
}

.luxury-alternative-image {
    width: 100%;
    height: 140px;
    background: #f8f9fa;
    border-radius: 12px;
    margin: 1rem 0;
    overflow: hidden;
    position: relative;
}

.luxury-alternative-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.luxury-alternative-image::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(45deg, transparent 0%, rgba(191, 161, 51, 0.1) 100%);
}

.luxury-alternative-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
}

.luxury-btn-view {
    flex: 1;
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.luxury-btn-view:hover {
    background: linear-gradient(135deg, #4b5563, #374151);
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
}



/* Secondary Button Style */
.luxury-btn-secondary {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: white;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
}

.luxury-btn-secondary:hover {
    background: linear-gradient(135deg, #0284c7, #0369a1);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
    color: white;
    text-decoration: none;
}

/* Loading State */
.luxury-alternatives-loading {
    text-align: center;
    padding: 3rem 2rem;
    color: #666;
}

.luxury-loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f4f6;
    border-left: 4px solid #bfa133;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

/* No Alternatives State */
.luxury-no-alternatives {
    text-align: center;
    padding: 3rem 2rem;
    color: #666;
}

.luxury-no-alternatives-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #9ca3af;
    margin: 0 auto 1rem;
}

.luxury-no-alternatives h6 {
    color: #374151;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.luxury-no-alternatives p {
    color: #6b7280;
    margin-bottom: 1.5rem;
}

@media (max-width: 767px) {
    .luxury-modal-body {
        padding: 1.5rem;
    }

    .luxury-pricing-grid,
    .luxury-summary-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .luxury-modal-footer {
        flex-direction: column;
    }

    .luxury-btn-cancel,
    .luxury-btn-confirm {
        width: 100%;
        justify-content: center;
    }

    /* Alternative Modal Responsive */
    .luxury-alternatives-grid {
        grid-template-columns: 1fr;
    }

    .luxury-alternative-card {
        padding: 1rem;
    }

    .luxury-alternative-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .luxury-alternative-info h6 {
        max-width: 100%;
        text-align: center;
        font-size: 0.9rem;
    }

    .luxury-alternative-price {
        text-align: center;
    }

        .luxury-alternative-actions {
        flex-direction: column;
    }

    .luxury-btn-view {
        flex: 1;
        width: 100%;
    }

    .responsive-car-details {
        margin-top: 64px !important;
        padding-left: 0.2rem;
        padding-right: 0.2rem;
    }
    .lux-pricing-compact .row > div {
        margin-bottom: 0.8rem;
    }
    .lux-pricing-compact {
        padding-left: 0.2rem;
        padding-right: 0.2rem;
    }
    .car-hero-img2 {
        max-width: 100%;
        max-height: 140px;
    }
}
</style>

@auth
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookNowBtn = document.getElementById('bookNowBtn');
    const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
    const alternativeVehiclesModal = new bootstrap.Modal(document.getElementById('alternativeVehiclesModal'));
    const bookingForm = document.getElementById('bookingForm');
    const submitBookingBtn = document.getElementById('submitBooking');
    const statusAlert = document.getElementById('statusAlert');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const totalDaysElement = document.getElementById('totalDays');
    const totalAmountElement = document.getElementById('totalAmount');

    const vehicleId = bookNowBtn.dataset.vehicleId;
    const vehicleStatus = bookNowBtn.dataset.vehicleStatus;
    const dailyRate = {{ $vehicle->daily_rate ?? 0 }};
    const weeklyRate = {{ $vehicle->weekly_rate ?? 0 }};
    const monthlyRate = {{ $vehicle->monthly_rate ?? 0 }};

    // Initialize current vehicle rates (can be updated for alternative vehicles)
    window.currentVehicleRates = {
        daily: dailyRate,
        weekly: weeklyRate,
        monthly: monthlyRate,
        vehicleId: vehicleId
    };

    // Check vehicle status when opening modal
    bookNowBtn.addEventListener('click', function() {
        if (vehicleStatus.toLowerCase() !== 'available') {
            // Show alternatives modal for unavailable vehicles
            showAlternativeVehiclesModal();
            return;
        }
        bookingModal.show();
    });

    // Function to show alternative vehicles modal
    function showAlternativeVehiclesModal() {
        // Show loading state
        document.getElementById('alternativesLoading').style.display = 'block';
        document.getElementById('alternativesGrid').style.display = 'none';
        document.getElementById('noAlternatives').classList.add('d-none');

        // Show the modal
        alternativeVehiclesModal.show();

        // Fetch alternative vehicles
        fetch(`/vehicles/${vehicleId}/alternatives`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.alternatives.length > 0) {
                    displayAlternativeVehicles(data.alternatives);
                } else {
                    showNoAlternatives();
                }
            })
            .catch(error => {
                console.error('Error fetching alternatives:', error);
                showNoAlternatives();
            })
            .finally(() => {
                document.getElementById('alternativesLoading').style.display = 'none';
            });
    }

    // Function to display alternative vehicles
    function displayAlternativeVehicles(alternatives) {
        const alternativesGrid = document.getElementById('alternativesGrid');

        alternativesGrid.innerHTML = alternatives.map(vehicle => {
            const priceDiff = vehicle.price_difference;
            const priceDiffPercent = vehicle.price_difference_percent;

            let priceBadge = '';
            if (priceDiff > 0) {
                priceBadge = `<div class="luxury-price-difference more-expensive">+${Math.abs(priceDiffPercent)}% more</div>`;
            } else if (priceDiff < 0) {
                priceBadge = `<div class="luxury-price-difference cheaper">${Math.abs(priceDiffPercent)}% less</div>`;
            } else {
                priceBadge = `<div class="luxury-price-difference same-price">Same price</div>`;
            }

            return `
                <div class="luxury-alternative-card">
                    <div class="luxury-alternative-header">
                        <div class="luxury-alternative-info">
                            <h6>${vehicle.make} ${vehicle.model}</h6>
                            <p>${vehicle.year || ''} • ${vehicle.category || 'Luxury'}</p>
                        </div>
                        <div class="luxury-alternative-price">
                            <div class="luxury-alternative-amount">AED ${numberFormat(vehicle.daily_rate)}</div>
                            <span class="luxury-alternative-period">per day</span>
                            ${priceBadge}
                        </div>
                    </div>

                    <div class="luxury-alternative-image">
                        <img src="${vehicle.image_url || '/asset/image.png'}" alt="${vehicle.make} ${vehicle.model}" onerror="this.src='/asset/image.png'">
                    </div>

                    <div class="luxury-alternative-specs">
                        <span class="luxury-spec-badge">${vehicle.seats || 5} Seats</span>
                        <span class="luxury-spec-badge">${vehicle.doors || 4} Doors</span>
                        <span class="luxury-spec-badge">${vehicle.transmission || 'Automatic'}</span>
                        <span class="luxury-spec-badge">${vehicle.color || 'N/A'}</span>
                    </div>

                                        <div class="luxury-alternative-actions">
                        <a href="/cars/${vehicle.id}" class="luxury-btn-view" style="flex: 1; width: 100%;">
                            <i class="bi bi-eye"></i>View Details
                        </a>
                    </div>
                </div>
            `;
        }).join('');

                alternativesGrid.style.display = 'grid';
    }

    // Function to show no alternatives message
    function showNoAlternatives() {
        document.getElementById('alternativesGrid').style.display = 'none';
        document.getElementById('noAlternatives').classList.remove('d-none');
    }

    // Global function to redirect to vehicle page
    window.redirectToVehicle = function(vehicleId) {
        window.location.href = `/cars/${vehicleId}`;
    };



    // Calculate total when dates change
    function calculateTotal() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const timeDiff = end.getTime() - start.getTime();
            const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // Include both start and end days

            if (daysDiff > 0) {
                let totalAmount = 0;
                let rateInfo = '';

                // تطبيق منطق التسعير حسب عدد الأيام باستخدام الأسعار الحالية
                const currentRates = window.currentVehicleRates;
                if (daysDiff <= 7) {
                    // 7 أيام أو أقل: السعر اليومي
                    totalAmount = daysDiff * currentRates.daily;
                    rateInfo = `Daily Rate (${daysDiff} ${daysDiff === 1 ? 'day' : 'days'})`;
                } else if (daysDiff > 7 && daysDiff < 28) {
                    // أكثر من 7 وأقل من 28: السعر الأسبوعي مقسوم على 7
                    const weeklyDailyRate = currentRates.weekly / 7;
                    totalAmount = daysDiff * weeklyDailyRate;
                    rateInfo = `Weekly Rate (AED ${numberFormat(Math.round(weeklyDailyRate))}/day × ${daysDiff} days)`;
                } else {
                    // 28 يوم أو أكثر: السعر الشهري مقسوم على 30
                    const monthlyDailyRate = Math.round(currentRates.monthly / 30);
                    totalAmount = daysDiff * monthlyDailyRate;
                    rateInfo = `Monthly Rate (AED ${numberFormat(monthlyDailyRate)}/day × ${daysDiff} days)`;
                }

                totalDaysElement.textContent = daysDiff + ' days';
                totalAmountElement.textContent = 'AED ' + numberFormat(Math.round(totalAmount));

                // إضافة معلومات نوع التسعير تحت المبلغ
                const rateInfoElement = document.getElementById('rateInfo');
                if (rateInfoElement) {
                    rateInfoElement.textContent = rateInfo;
                } else {
                    // إنشاء عنصر جديد لعرض معلومات التسعير
                    const newRateInfo = document.createElement('div');
                    newRateInfo.id = 'rateInfo';
                    newRateInfo.className = 'small text-muted mt-1';
                    newRateInfo.textContent = rateInfo;
                    totalAmountElement.parentNode.appendChild(newRateInfo);
                }

                // Check availability for these dates
                checkAvailability(startDate, endDate);
                    } else {
            totalDaysElement.textContent = '-';
            totalAmountElement.textContent = 'AED -';

            // إخفاء معلومات التسعير إذا لم تكن هناك تواريخ
            const rateInfoElement = document.getElementById('rateInfo');
            if (rateInfoElement) {
                rateInfoElement.textContent = '';
            }
        }
        }
    }

    // Check availability
    function checkAvailability(startDate, endDate) {
        const currentVehicleId = window.currentVehicleRates.vehicleId;
        fetch(`/vehicles/${currentVehicleId}/availability?start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => {
                if (!data.available) {
                    showAlert('danger', 'Vehicle is not available for the selected dates. Please choose different dates.');
                    submitBookingBtn.disabled = true;
                } else {
                    hideAlert();
                    submitBookingBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error checking availability:', error);
                showAlert('warning', 'Unable to check availability. Please try again.');
            });
    }

    // Submit booking
    submitBookingBtn.addEventListener('click', function() {
        const formData = new FormData(bookingForm);

        submitBookingBtn.disabled = true;
        submitBookingBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>Processing...';

        fetch('/bookings', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.redirect_url) {
                // Show success message and redirect to summary
                showAlert('success', 'Redirecting to booking summary...');

                // Redirect to summary page after short delay
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1000);
            } else if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => {
                    bookingModal.hide();
                    // Redirect to bookings page
                    window.location.href = '/my-bookings-page';
                }, 2000);
            } else {
                showAlert('danger', data.message);
                submitBookingBtn.disabled = false;
                submitBookingBtn.innerHTML = '<i class="bi bi-check-circle"></i>Confirm Booking';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred. Please try again.');
            submitBookingBtn.disabled = false;
            submitBookingBtn.innerHTML = '<i class="bi bi-check-circle"></i>Confirm Booking';
        });
    });

    // Event listeners for date changes
    startDateInput.addEventListener('change', calculateTotal);
    endDateInput.addEventListener('change', calculateTotal);

    // Update end date minimum when start date changes
    startDateInput.addEventListener('change', function() {
        const startDate = new Date(this.value);
        const nextDay = new Date(startDate);
        nextDay.setDate(startDate.getDate() + 1);
        endDateInput.min = nextDay.toISOString().split('T')[0];

        if (endDateInput.value && new Date(endDateInput.value) <= startDate) {
            endDateInput.value = '';
        }
    });

    // Helper functions
    function showAlert(type, message) {
        statusAlert.className = `alert alert-${type}`;
        statusAlert.textContent = message;
        statusAlert.classList.remove('d-none');
    }

    function hideAlert() {
        statusAlert.classList.add('d-none');
    }

    function numberFormat(num) {
        return new Intl.NumberFormat().format(num);
    }
});
</script>
@endauth

@endsection
