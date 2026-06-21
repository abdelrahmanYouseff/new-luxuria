@extends('layouts.blade_app')

@php $isRtl = app()->getLocale() === 'ar'; @endphp
@section('title', __('app.meta_booking_summary_title'))
@section('meta_description', __('app.meta_booking_summary_desc'))
@section('robots', 'noindex, nofollow')

@section('content')
<div class="bsp">

    {{-- ── TOP HERO STRIP ── --}}
    <div class="bsp-hero">
        <div class="bsp-hero-inner">
            <div class="bsp-badge">
                <i class="bi bi-shield-check"></i>
                Secure Checkout
            </div>
            <h1 class="bsp-hero-title">Booking Summary</h1>
            <p class="bsp-hero-sub">Review your details and complete payment securely</p>

            {{-- Progress Steps --}}
            <div class="bsp-steps">
                <div class="bsp-step done"><span class="bsp-step-dot"><i class="bi bi-check"></i></span><span class="bsp-step-lbl">Vehicle</span></div>
                <div class="bsp-step-line done"></div>
                <div class="bsp-step done"><span class="bsp-step-dot"><i class="bi bi-check"></i></span><span class="bsp-step-lbl">Details</span></div>
                <div class="bsp-step-line active"></div>
                <div class="bsp-step active"><span class="bsp-step-dot"><i class="bi bi-credit-card"></i></span><span class="bsp-step-lbl">Payment</span></div>
                <div class="bsp-step-line"></div>
                <div class="bsp-step"><span class="bsp-step-dot"><i class="bi bi-check-circle"></i></span><span class="bsp-step-lbl">Done</span></div>
            </div>
        </div>
    </div>

    {{-- ── MAIN CONTENT ── --}}
    <div class="bsp-body">
        <div class="bsp-grid">

            {{-- ─── LEFT COLUMN ─── --}}
            <div class="bsp-left">

                {{-- Alerts --}}
                @if(isset($externalBookingResult))
                    @if($externalBookingResult['success'])
                        <div class="bsp-alert bsp-alert-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <div><strong>External booking created</strong><br><small>{{ $externalBookingResult['message'] }}</small></div>
                        </div>
                    @else
                        <div class="bsp-alert bsp-alert-warn">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div><strong>External booking warning</strong><br><small>{{ $externalBookingResult['message'] }}</small></div>
                        </div>
                    @endif
                @endif
                @if(isset($booking))
                    <div class="bsp-alert bsp-alert-info">
                        <i class="bi bi-info-circle-fill"></i>
                        <div>
                            <strong>Booking #{{ $booking->id }}</strong>&nbsp;&nbsp;
                            <span class="badge {{ $booking->status_badge_class }}">{{ $booking->formatted_status }}</span>
                            @if($booking->external_reservation_id)<br><small>External ID: {{ $booking->external_reservation_id }}</small>@endif
                        </div>
                    </div>
                @endif

                {{-- ── VEHICLE CARD ── --}}
                <div class="bsp-card">
                    <div class="bsp-card-head">
                        <span class="bsp-card-icon"><i class="bi bi-car-front-fill"></i></span>
                        <span class="bsp-card-title">Vehicle</span>
                    </div>
                    <div class="bsp-card-body">
                        <div class="bsp-vehicle">
                            <div class="bsp-vehicle-img-wrap">
                                <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->make }} {{ $vehicle->model }}" onerror="this.src='/asset/image.png'" class="bsp-vehicle-img">
                                <span class="bsp-vehicle-cat">{{ ucfirst($vehicle->category) }}</span>
                            </div>
                            <div class="bsp-vehicle-info">
                                <h2 class="bsp-vehicle-name">{{ $vehicle->make ?? '' }} {{ $vehicle->model ?? '' }}</h2>
                                <p class="bsp-vehicle-year">{{ $vehicle->year ?? 'Latest Model' }}</p>
                                <div class="bsp-specs">
                                    <span class="bsp-spec"><i class="bi bi-people-fill"></i>{{ $vehicle->seats ?? 5 }} Seats</span>
                                    <span class="bsp-spec"><i class="bi bi-door-open-fill"></i>{{ $vehicle->doors ?? 4 }} Doors</span>
                                    <span class="bsp-spec"><i class="bi bi-gear-wide-connected"></i>{{ $vehicle->transmission ?? 'Automatic' }}</span>
                                    <span class="bsp-spec"><i class="bi bi-palette-fill"></i>{{ $vehicle->color ?? 'Premium' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── RENTAL DETAILS CARD ── --}}
                <div class="bsp-card">
                    <div class="bsp-card-head">
                        <span class="bsp-card-icon"><i class="bi bi-calendar3"></i></span>
                        <span class="bsp-card-title">Rental Details</span>
                    </div>
                    <div class="bsp-card-body">
                        <div class="bsp-info-pills">
                            <div class="bsp-info-pill">
                                <i class="bi bi-geo-alt-fill"></i>
                                <div>
                                    <span class="bsp-pill-label">Pick-up Location</span>
                                    <span class="bsp-pill-val">{{ $bookingData['emirate'] ?? 'Not specified' }}</span>
                                </div>
                            </div>
                            <div class="bsp-info-pill">
                                <i class="bi bi-clock-fill"></i>
                                <div>
                                    <span class="bsp-pill-label">Duration</span>
                                    <span class="bsp-pill-val">{{ $bookingData['total_days'] ?? 0 }} days</span>
                                </div>
                            </div>
                        </div>

                        <div class="bsp-dates">
                            <div class="bsp-date-block">
                                <span class="bsp-date-tag bsp-date-from">FROM</span>
                                <i class="bi bi-calendar-event-fill bsp-date-icon"></i>
                                @if(isset($bookingData['start_date']))
                                    <span class="bsp-date-main">{{ \Carbon\Carbon::parse($bookingData['start_date'])->format('D, M j') }}</span>
                                    <span class="bsp-date-year">{{ \Carbon\Carbon::parse($bookingData['start_date'])->format('Y') }}</span>
                                @else
                                    <span class="bsp-date-main">–</span>
                                @endif
                            </div>
                            <div class="bsp-dates-arrow"><i class="bi bi-arrow-right-short"></i></div>
                            <div class="bsp-date-block">
                                <span class="bsp-date-tag bsp-date-to">TO</span>
                                <i class="bi bi-calendar-check-fill bsp-date-icon"></i>
                                @if(isset($bookingData['end_date']))
                                    <span class="bsp-date-main">{{ \Carbon\Carbon::parse($bookingData['end_date'])->format('D, M j') }}</span>
                                    <span class="bsp-date-year">{{ \Carbon\Carbon::parse($bookingData['end_date'])->format('Y') }}</span>
                                @else
                                    <span class="bsp-date-main">–</span>
                                @endif
                            </div>
                        </div>

                        @if(isset($bookingData['notes']) && $bookingData['notes'])
                            <div class="bsp-note">
                                <i class="bi bi-chat-quote-fill"></i>
                                <div><span class="bsp-pill-label">Special Requests</span><span class="bsp-pill-val">{{ $bookingData['notes'] }}</span></div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── CUSTOMER CARD ── --}}
                <div class="bsp-card">
                    <div class="bsp-card-head">
                        <span class="bsp-card-icon"><i class="bi bi-person-fill"></i></span>
                        <span class="bsp-card-title">Customer</span>
                    </div>
                    <div class="bsp-card-body">
                        <div class="bsp-customer">
                            <div class="bsp-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                            <div class="bsp-customer-info">
                                <h3 class="bsp-customer-name">{{ Auth::user()->name ?? 'User' }}</h3>
                                <p><i class="bi bi-envelope-fill"></i>{{ Auth::user()->email ?? '' }}</p>
                                @if(Auth::user()->emirate)<p><i class="bi bi-geo-alt-fill"></i>{{ Auth::user()->emirate }}</p>@endif
                                @if(Auth::user()->address)<p><i class="bi bi-house-fill"></i>{{ Auth::user()->address }}</p>@endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── PAYMENT METHOD CARD ── --}}
                <div class="bsp-card">
                    <div class="bsp-card-head">
                        <span class="bsp-card-icon"><i class="bi bi-lock-fill"></i></span>
                        <span class="bsp-card-title">Payment Method</span>
                        <span class="bsp-pm-secure-badge"><i class="bi bi-shield-check"></i> SSL Secured</span>
                    </div>
                    <div class="bsp-card-body">
                        <div class="bsp-pmethods">

                            {{-- Card --}}
                            <div class="bsp-pmethod active" data-method="stripe">
                                <div class="bsp-pm-icon bsp-pm-icon--card">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="5" width="20" height="14" rx="3" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M2 10h20" stroke="currentColor" stroke-width="1.8"/>
                                        <rect x="5" y="14" width="4" height="2" rx="1" fill="currentColor"/>
                                        <rect x="11" y="14" width="3" height="2" rx="1" fill="currentColor"/>
                                    </svg>
                                </div>
                                <div class="bsp-pm-info">
                                    <span class="bsp-pm-name">Credit / Debit Card</span>
                                    <span class="bsp-pm-sub">Stripe · 256-bit encrypted</span>
                                    <div class="bsp-pm-logos">
                                        <img src="https://cdn.worldvectorlogo.com/logos/visa-10.svg" alt="Visa" class="bsp-logo-visa">
                                        <img src="https://cdn.worldvectorlogo.com/logos/mastercard-6.svg" alt="Mastercard" class="bsp-logo-mc">
                                        <img src="https://cdn.worldvectorlogo.com/logos/american-express-3.svg" alt="Amex" class="bsp-logo-amex">
                                    </div>
                                </div>
                                <div class="bsp-pm-radio">
                                    <span class="bsp-radio-dot"></span>
                                </div>
                            </div>

                            {{-- Bank --}}
                            <div class="bsp-pmethod" data-method="bank">
                                <div class="bsp-pm-icon bsp-pm-icon--bank">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 21h18M3 10h18M5 10V21M9 10V21M15 10V21M19 10V21M12 3L3 10h18L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="bsp-pm-info">
                                    <span class="bsp-pm-name">Bank Transfer</span>
                                    <span class="bsp-pm-sub">Direct transfer · 1–2 business days</span>
                                </div>
                                <div class="bsp-pm-radio">
                                    <span class="bsp-radio-dot"></span>
                                </div>
                            </div>

                            {{-- Wallet --}}
                            <div class="bsp-pmethod" data-method="wallet">
                                <div class="bsp-pm-icon bsp-pm-icon--wallet">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 7H4C2.9 7 2 7.9 2 9V19C2 20.1 2.9 21 4 21H20C21.1 21 22 20.1 22 19V9C22 7.9 21.1 7 20 7Z" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M16 7V5C16 3.9 15.1 3 14 3H10C8.9 3 8 3.9 8 5V7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <circle cx="17" cy="14" r="1.5" fill="currentColor"/>
                                    </svg>
                                </div>
                                <div class="bsp-pm-info">
                                    <span class="bsp-pm-name">Digital Wallet</span>
                                    <div class="bsp-wallet-row">
                                        <span class="bsp-wallet-chip bsp-wallet-apple">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                                            Apple Pay
                                        </span>
                                        <span class="bsp-wallet-chip bsp-wallet-google">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                            Google Pay
                                        </span>
                                    </div>
                                </div>
                                <div class="bsp-pm-radio">
                                    <span class="bsp-radio-dot"></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>{{-- end left --}}

            {{-- ─── RIGHT COLUMN — PRICE SUMMARY ─── --}}
            <div class="bsp-right">
                <div class="bsp-price-card" id="bspPriceCard">
                    <div class="bsp-price-head">
                        <span>Order Summary</span>
                        <span class="bsp-price-tag">AED {{ number_format($bookingData['total_amount'] ?? 0, 2) }}</span>
                    </div>

                    <div class="bsp-price-rows">
                        <div class="bsp-price-row">
                            <span>Base Rate <em>({{ $bookingData['pricing_type'] ?? 'daily' }})</em></span>
                            <span>AED {{ number_format($bookingData['applied_rate'] ?? 0, 2) }}</span>
                        </div>
                        <div class="bsp-price-row">
                            <span>Duration</span>
                            <span>{{ $bookingData['total_days'] ?? 0 }} days</span>
                        </div>
                        <div class="bsp-price-row bsp-price-sub">
                            <span>Subtotal</span>
                            <span>AED {{ number_format($bookingData['total_amount'] ?? 0, 2) }}</span>
                        </div>
                        <div class="bsp-price-row">
                            <span>Tax & Fees</span>
                            <span>AED 0.00</span>
                        </div>
                    </div>

                    <div class="bsp-price-total">
                        <span>Total</span>
                        <span>AED {{ number_format($bookingData['total_amount'] ?? 0, 2) }}</span>
                    </div>

                    <div class="bsp-trust-row">
                        <div class="bsp-trust"><i class="bi bi-shield-lock-fill"></i>Secure</div>
                        <div class="bsp-trust"><i class="bi bi-arrow-counterclockwise"></i>Free Cancel</div>
                        <div class="bsp-trust"><i class="bi bi-headset"></i>24/7 Support</div>
                    </div>

                    {{-- AJAX submit keeps this page in browser history so Back from Stripe works --}}
                    <form id="paymentForm">
                        @csrf
                        @if(isset($booking))
                            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        @else
                            <input type="hidden" name="booking_data" value="{{ json_encode($bookingData) }}">
                        @endif
                        <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="stripe">

                        <button type="submit" class="bsp-pay-btn" id="submitButton">
                            <span class="bsp-pay-left"><i class="bi bi-lock-fill"></i> Proceed to Payment</span>
                            <span class="bsp-pay-amount">AED {{ number_format($bookingData['total_amount'] ?? 0, 2) }}</span>
                        </button>
                    </form>

                    <div id="bspPayError" class="bsp-pay-error" style="display:none;"></div>

                    <p class="bsp-secure-note"><i class="bi bi-shield-check"></i> Your payment is encrypted and secure</p>
                </div>
            </div>

        </div>{{-- end grid --}}
    </div>{{-- end body --}}
</div>

<style>
/* ─── BASE ─── */
.bsp {
    background: #f5f5f7;
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', system-ui, sans-serif;
}

/* ─── HERO ─── */
.bsp-hero {
    background: linear-gradient(160deg, #1a1a1a 0%, #2d2d2d 60%, #1a1a1a 100%);
    padding: 100px 0 56px;
    text-align: center;
}
.bsp-hero-inner { max-width: 680px; margin: 0 auto; padding: 0 20px; }
.bsp-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(191,161,51,0.15);
    border: 1px solid rgba(191,161,51,0.3);
    color: #bfa133;
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 5px 14px;
    border-radius: 100px;
    margin-bottom: 18px;
}
.bsp-hero-title {
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.02em;
    margin-bottom: 10px;
}
.bsp-hero-sub {
    font-size: 1rem;
    color: rgba(255,255,255,0.5);
    margin-bottom: 36px;
}

/* ─── PROGRESS STEPS ─── */
.bsp-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
}
.bsp-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}
.bsp-step-dot {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.35);
    background: transparent;
    transition: all 0.3s;
}
.bsp-step.done .bsp-step-dot {
    background: #bfa133;
    border-color: #bfa133;
    color: #fff;
}
.bsp-step.active .bsp-step-dot {
    background: #fff;
    border-color: #fff;
    color: #1a1a1a;
    box-shadow: 0 0 0 4px rgba(255,255,255,0.15);
}
.bsp-step-lbl {
    font-size: 0.72rem;
    font-weight: 500;
    color: rgba(255,255,255,0.35);
    letter-spacing: 0.03em;
}
.bsp-step.done .bsp-step-lbl,
.bsp-step.active .bsp-step-lbl { color: rgba(255,255,255,0.75); }
.bsp-step-line {
    width: 60px;
    height: 2px;
    background: rgba(255,255,255,0.1);
    margin: 0 6px;
    margin-bottom: 18px;
    flex-shrink: 0;
}
.bsp-step-line.done { background: #bfa133; }
.bsp-step-line.active { background: linear-gradient(90deg, #bfa133, rgba(255,255,255,0.3)); }

/* ─── BODY ─── */
.bsp-body { max-width: 1140px; margin: 0 auto; padding: 40px 20px 80px; }
.bsp-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 28px;
    align-items: start;
}

/* ─── ALERTS ─── */
.bsp-alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px 20px;
    border-radius: 14px;
    margin-bottom: 16px;
    font-size: 0.9rem;
}
.bsp-alert i { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
.bsp-alert-success { background: #d1fae5; color: #065f46; }
.bsp-alert-warn { background: #fef3c7; color: #92400e; }
.bsp-alert-info { background: #eff6ff; color: #1e40af; }

/* ─── CARDS ─── */
.bsp-card {
    background: #fff;
    border-radius: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
    overflow: hidden;
}
.bsp-card-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 20px 24px 16px;
    border-bottom: 1px solid #f2f2f7;
}
.bsp-card-icon {
    width: 34px;
    height: 34px;
    background: #f5f5f7;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    color: #bfa133;
}
.bsp-card-title {
    font-size: 1.05rem;
    font-weight: 600;
    color: #1a1a1a;
    letter-spacing: -0.01em;
}
.bsp-card-body { padding: 24px; }

/* ─── VEHICLE ─── */
.bsp-vehicle { display: flex; gap: 24px; align-items: flex-start; }
.bsp-vehicle-img-wrap { position: relative; flex-shrink: 0; }
.bsp-vehicle-img {
    width: 190px;
    height: 130px;
    object-fit: cover;
    border-radius: 14px;
    background: #f5f5f7;
}
.bsp-vehicle-cat {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(191,161,51,0.9);
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 100px;
    backdrop-filter: blur(4px);
}
.bsp-vehicle-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: -0.02em;
    margin-bottom: 4px;
}
.bsp-vehicle-year { font-size: 0.92rem; color: #bfa133; font-weight: 500; margin-bottom: 16px; }
.bsp-specs { display: flex; flex-wrap: wrap; gap: 8px; }
.bsp-spec {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f5f5f7;
    color: #444;
    font-size: 0.82rem;
    font-weight: 500;
    padding: 5px 12px;
    border-radius: 100px;
}
.bsp-spec i { color: #bfa133; font-size: 0.78rem; }

/* ─── INFO PILLS ─── */
.bsp-info-pills { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
.bsp-info-pill {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f5f5f7;
    border-radius: 14px;
    padding: 14px 16px;
}
.bsp-info-pill > i { font-size: 1.1rem; color: #bfa133; flex-shrink: 0; }
.bsp-pill-label { display: block; font-size: 0.72rem; color: #888; font-weight: 500; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 3px; }
.bsp-pill-val { display: block; font-size: 0.98rem; font-weight: 600; color: #1a1a1a; }

/* ─── DATES ─── */
.bsp-dates {
    display: flex;
    align-items: center;
    gap: 0;
    background: #f5f5f7;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 16px;
}
.bsp-date-block {
    flex: 1;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
}
.bsp-date-block:first-child { border-right: 1px solid #e5e5ea; }
.bsp-date-tag {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 100px;
}
.bsp-date-from { background: #d1fae5; color: #065f46; }
.bsp-date-to   { background: #fee2e2; color: #991b1b; }
.bsp-date-icon { font-size: 1.1rem; color: #bfa133; }
.bsp-date-main { font-size: 1.05rem; font-weight: 700; color: #1a1a1a; }
.bsp-date-year { font-size: 0.8rem; color: #888; }
.bsp-dates-arrow {
    font-size: 1.6rem;
    color: #bfa133;
    padding: 0 4px;
    flex-shrink: 0;
}

/* Note */
.bsp-note {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: #eff6ff;
    border-radius: 12px;
    padding: 14px 16px;
}
.bsp-note > i { color: #3b82f6; font-size: 1rem; flex-shrink: 0; margin-top: 2px; }

/* ─── CUSTOMER ─── */
.bsp-customer { display: flex; align-items: center; gap: 18px; }
.bsp-avatar {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #bfa133, #d4b846);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}
.bsp-customer-name { font-size: 1.15rem; font-weight: 700; color: #1a1a1a; margin-bottom: 6px; }
.bsp-customer-info p {
    font-size: 0.88rem;
    color: #555;
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 5px;
}
.bsp-customer-info i { color: #bfa133; width: 14px; font-size: 0.82rem; }

/* ─── PAYMENT METHODS ─── */
.bsp-pm-secure-badge {
    margin-left: auto;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #16a34a;
    background: #dcfce7;
    padding: 3px 10px;
    border-radius: 100px;
    letter-spacing: 0.02em;
}
.bsp-pm-secure-badge i { font-size: 0.75rem; }

.bsp-pmethods { display: flex; flex-direction: column; gap: 10px; }

.bsp-pmethod {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    border: 1.5px solid #e5e5ea;
    border-radius: 16px;
    cursor: pointer;
    transition: border-color 0.22s, background 0.22s, box-shadow 0.22s;
    background: #fff;
    position: relative;
}
.bsp-pmethod:hover { border-color: rgba(191,161,51,0.5); background: #fffef5; }
.bsp-pmethod.active {
    border-color: #bfa133;
    background: linear-gradient(135deg, #fffef0, #fffbe6);
    box-shadow: 0 0 0 3px rgba(191,161,51,0.1);
}

/* Icon squares */
.bsp-pm-icon {
    width: 48px;
    height: 48px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background 0.22s;
}
.bsp-pm-icon--card  { background: #f0f4ff; color: #3b5bdb; }
.bsp-pm-icon--bank  { background: #f0fdf4; color: #16a34a; }
.bsp-pm-icon--wallet{ background: #fdf4ff; color: #9333ea; }
.bsp-pmethod.active .bsp-pm-icon--card   { background: #dbe4ff; }
.bsp-pmethod.active .bsp-pm-icon--bank   { background: #bbf7d0; }
.bsp-pmethod.active .bsp-pm-icon--wallet { background: #f3e8ff; }

.bsp-pm-info { flex: 1; min-width: 0; }
.bsp-pm-name { display: block; font-size: 0.95rem; font-weight: 600; color: #1a1a1a; margin-bottom: 2px; }
.bsp-pm-sub  { display: block; font-size: 0.78rem; color: #888; margin-bottom: 0; }

/* Card logos */
.bsp-pm-logos { display: flex; gap: 6px; margin-top: 9px; align-items: center; }
.bsp-logo-visa  { height: 14px; width: auto; }
.bsp-logo-mc    { height: 22px; width: auto; }
.bsp-logo-amex  { height: 22px; width: auto; }

/* Wallet chips */
.bsp-wallet-row { display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap; }
.bsp-wallet-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 100px;
    white-space: nowrap;
}
.bsp-wallet-apple  { background: #f5f5f7; color: #1a1a1a; border: 1px solid #e5e5ea; }
.bsp-wallet-google { background: #fff; color: #444; border: 1px solid #e5e5ea; }

/* Custom radio */
.bsp-pm-radio {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid #d1d5db;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: border-color 0.2s;
}
.bsp-pmethod.active .bsp-pm-radio { border-color: #bfa133; }
.bsp-radio-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #bfa133;
    opacity: 0;
    transform: scale(0);
    transition: opacity 0.2s, transform 0.2s;
}
.bsp-pmethod.active .bsp-radio-dot { opacity: 1; transform: scale(1); }

/* ─── PRICE CARD ─── */
.bsp-price-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
    overflow: hidden;
    position: sticky;
    top: 90px;
}
.bsp-price-head {
    background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
    padding: 22px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.bsp-price-head span:first-child {
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.5);
}
.bsp-price-tag {
    font-size: 1.4rem;
    font-weight: 800;
    color: #bfa133;
    letter-spacing: -0.02em;
}
.bsp-price-rows { padding: 6px 24px 0; }
.bsp-price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 13px 0;
    font-size: 0.92rem;
    color: #444;
    border-bottom: 1px solid #f2f2f7;
}
.bsp-price-row:last-child { border-bottom: none; }
.bsp-price-row em { color: #888; font-style: normal; font-size: 0.85rem; }
.bsp-price-sub { font-weight: 600; color: #1a1a1a; }
.bsp-price-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 24px;
    padding: 18px 0 16px;
    border-top: 2px solid #f2f2f7;
    font-size: 1.1rem;
    font-weight: 800;
    color: #1a1a1a;
}
.bsp-price-total span:last-child { color: #bfa133; font-size: 1.25rem; }

.bsp-trust-row {
    display: flex;
    justify-content: space-around;
    padding: 12px 16px 16px;
    gap: 4px;
}
.bsp-trust {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    font-size: 0.7rem;
    color: #888;
    font-weight: 500;
    text-align: center;
}
.bsp-trust i { font-size: 1rem; color: #bfa133; }

.bsp-pay-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: calc(100% - 48px);
    margin: 0 24px 16px;
    padding: 15px 20px;
    background: linear-gradient(135deg, #bfa133, #d4b846);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
    box-shadow: 0 4px 18px rgba(191,161,51,0.3);
    min-height: 52px;
}
.bsp-pay-btn:hover {
    opacity: 0.92;
    transform: translateY(-1px);
    box-shadow: 0 6px 24px rgba(191,161,51,0.4);
}
.bsp-pay-left { display: flex; align-items: center; gap: 8px; font-size: 0.92rem; color: #fff; }
.bsp-pay-left i { font-size: 0.85rem; }
.bsp-pay-amount { font-size: 1rem; font-weight: 900; color: #fff; }

.bsp-secure-note {
    text-align: center;
    font-size: 0.78rem;
    color: #aaa;
    padding: 0 24px 20px;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.bsp-secure-note i { color: #bfa133; }

.bsp-pay-error {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fee2e2;
    color: #991b1b;
    font-size: 0.85rem;
    font-weight: 500;
    padding: 10px 24px;
    margin: 0 0 12px;
    border-top: 1px solid #fecaca;
}
@media (max-width: 767px) {
    .bsp-pay-error { padding: 10px 18px; }
}

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
    .bsp-grid { grid-template-columns: 1fr 340px; gap: 20px; }
}

@media (max-width: 840px) {
    .bsp-grid { grid-template-columns: 1fr; }
    .bsp-right { order: -1; }
    .bsp-price-card { position: static; }
    .bsp-hero { padding: 80px 0 44px; }
    .bsp-step-line { width: 36px; }
}

@media (max-width: 767px) {
    .bsp-hero { padding: 72px 0 36px; }
    .bsp-hero-title { font-size: 1.65rem; }
    .bsp-hero-sub { font-size: 0.88rem; margin-bottom: 28px; }
    .bsp-badge { font-size: 0.72rem; padding: 4px 11px; }

    .bsp-steps { gap: 0; }
    .bsp-step-dot { width: 30px; height: 30px; font-size: 0.75rem; }
    .bsp-step-lbl { font-size: 0.65rem; }
    .bsp-step-line { width: 22px; margin: 0 2px; margin-bottom: 16px; }

    .bsp-body { padding: 24px 14px 60px; }
    .bsp-grid { gap: 16px; }

    .bsp-card { border-radius: 16px; margin-bottom: 14px; }
    .bsp-card-head { padding: 16px 18px 14px; }
    .bsp-card-body { padding: 18px; }
    .bsp-card-icon { width: 30px; height: 30px; font-size: 0.85rem; }
    .bsp-card-title { font-size: 0.95rem; }

    /* Vehicle — stack image on top */
    .bsp-vehicle { flex-direction: column; gap: 16px; }
    .bsp-vehicle-img-wrap { width: 100%; }
    .bsp-vehicle-img { width: 100%; height: 190px; }
    .bsp-vehicle-name { font-size: 1.25rem; }

    /* Info pills */
    .bsp-info-pills { grid-template-columns: 1fr; gap: 10px; margin-bottom: 14px; }

    /* Dates */
    .bsp-dates { flex-direction: column; border-radius: 14px; }
    .bsp-date-block:first-child { border-right: none; border-bottom: 1px solid #e5e5ea; }
    .bsp-date-block { padding: 14px 16px; }
    .bsp-dates-arrow { display: none; }

    /* Customer */
    .bsp-customer { gap: 14px; }
    .bsp-avatar { width: 48px; height: 48px; font-size: 1.1rem; }
    .bsp-customer-name { font-size: 1rem; }
    .bsp-customer-info p { font-size: 0.82rem; }

    /* Payment method */
    .bsp-pm-secure-badge { display: none; }
    .bsp-pmethod { padding: 13px 14px; gap: 11px; border-radius: 14px; }
    .bsp-pm-icon { width: 42px; height: 42px; border-radius: 11px; }
    .bsp-pm-icon svg { width: 19px; height: 19px; }
    .bsp-pm-name { font-size: 0.88rem; }
    .bsp-pm-sub  { font-size: 0.74rem; }
    .bsp-logo-visa { height: 12px; }
    .bsp-logo-mc, .bsp-logo-amex { height: 18px; }
    .bsp-wallet-chip { font-size: 0.68rem; padding: 2px 7px; }
    .bsp-pm-radio { width: 20px; height: 20px; }
    .bsp-radio-dot { width: 9px; height: 9px; }

    /* Price card */
    .bsp-price-head { padding: 18px 18px; }
    .bsp-price-tag { font-size: 1.2rem; }
    .bsp-price-rows { padding: 4px 18px 0; }
    .bsp-price-row { font-size: 0.88rem; padding: 11px 0; }
    .bsp-price-total { margin: 0 18px; font-size: 1rem; }
    .bsp-price-total span:last-child { font-size: 1.1rem; }
    .bsp-trust-row { padding: 10px 10px 12px; }
    .bsp-trust { font-size: 0.65rem; }
    .bsp-pay-btn { width: calc(100% - 36px); margin: 0 18px 14px; padding: 13px 16px; border-radius: 12px; min-height: 48px; }
    .bsp-pay-left { font-size: 0.85rem; }
    .bsp-pay-amount { font-size: 0.92rem; }
    .bsp-secure-note { font-size: 0.72rem; padding: 0 18px 16px; }
}

@media (max-width: 480px) {
    .bsp-hero { padding: 68px 0 30px; }
    .bsp-hero-title { font-size: 1.4rem; }
    .bsp-badge { display: none; }
    .bsp-step-dot { width: 26px; height: 26px; }
    .bsp-step-lbl { display: none; }
    .bsp-step-line { width: 16px; }

    .bsp-body { padding: 18px 12px 50px; }
    .bsp-card-body { padding: 14px; }
    .bsp-vehicle-img { height: 160px; }
    .bsp-spec { font-size: 0.76rem; padding: 4px 10px; }
    .bsp-info-pill { padding: 11px 12px; }
    .bsp-pill-val { font-size: 0.9rem; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Payment method toggle
    document.querySelectorAll('.bsp-pmethod').forEach(function (opt) {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.bsp-pmethod').forEach(function (o) {
                o.classList.remove('active');
            });
            this.classList.add('active');
            document.getElementById('selectedPaymentMethod').value = this.dataset.method;
        });
    });

    // ── Keep this page in browser history so Back from Stripe returns here ──
    // We submit the form via AJAX, get the Stripe URL, then navigate with
    // window.location.href (instead of a server-side redirect). That way
    // the browser back-stack contains the booking summary page and pressing
    // Back on Stripe brings the user straight back here — not to login.

    sessionStorage.setItem('booking_summary_url', window.location.href);

    // Re-enable button when browser restores page from bfcache (back navigation)
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            var btn = document.getElementById('submitButton');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML =
                    '<span class="bsp-pay-left"><i class="bi bi-lock-fill"></i> Proceed to Payment</span>' +
                    '<span class="bsp-pay-amount">AED {{ number_format($bookingData["total_amount"] ?? 0, 2) }}</span>';
            }
            var err = document.getElementById('bspPayError');
            if (err) err.style.display = 'none';
        }
    });

    // AJAX form submission
    var form = document.getElementById('paymentForm');
    var btn  = document.getElementById('submitButton');
    var errBox = document.getElementById('bspPayError');

    if (form && btn) {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // prevent full-page navigation

            btn.disabled = true;
            btn.innerHTML = '<span class="bsp-pay-left"><i class="bi bi-hourglass-split"></i> Processing…</span>';
            if (errBox) errBox.style.display = 'none';

            var formData = new FormData(form);
            var data = {};
            formData.forEach(function (v, k) { data[k] = v; });

            fetch('{{ route("booking.confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify(data),
            })
            .then(function (res) {
                if (res.redirected) {
                    // Server returned a redirect (e.g. to Stripe) — follow it
                    // using location.href so booking summary stays in back-stack
                    window.location.href = res.url;
                    return;
                }
                return res.json().then(function (json) {
                    if (json && json.redirect) {
                        window.location.href = json.redirect;
                    } else if (json && json.payment_url) {
                        window.location.href = json.payment_url;
                    } else {
                        throw new Error(json.error || json.message || 'Payment failed. Please try again.');
                    }
                });
            })
            .catch(function (err) {
                btn.disabled = false;
                btn.innerHTML =
                    '<span class="bsp-pay-left"><i class="bi bi-lock-fill"></i> Proceed to Payment</span>' +
                    '<span class="bsp-pay-amount">AED {{ number_format($bookingData["total_amount"] ?? 0, 2) }}</span>';
                if (errBox) {
                    errBox.textContent = err.message || 'Something went wrong. Please try again.';
                    errBox.style.display = 'flex';
                }
                console.error('Payment error:', err);
            });
        });
    }
});
</script>
@endsection
