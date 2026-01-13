@extends('layouts.blade_app')

@section('title', 'Welcome to Luxuria UAE')
@section('meta_description', 'Welcome to Luxuria UAE - Premium luxury car rental service in Dubai. Rent luxury, sports, classic, and economy vehicles with exceptional service and competitive pricing.')

@section('content')
<!-- Hero Section with Video Background -->
<div class="hero-video-section position-relative overflow-hidden">
    <video class="hero-bg-video" autoplay loop muted playsinline>
        <source src="https://duruthemes.com/demo/html/renax/video.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
    <div class="container h-100 position-relative z-2">
        <div class="row align-items-center justify-content-center h-100" style="min-height: 420px;">


        </div>
    </div>
</div>
<!-- شريط الشعارات المتحرك أسفل الفيديو -->
<div class="brands-marquee-outer">
    <div class="brands-marquee-wrapper">
        <div class="brands-marquee">
            <img src="/images_car/1.png" alt="Car Brand" />
            <img src="/images_car/2.png" alt="Car Brand" />
            <img src="/images_car/3.png" alt="Car Brand" />
            <img src="/images_car/4.png" alt="Car Brand" />
            <img src="/images_car/5.png" alt="Car Brand" />
            <img src="/images_car/6.png" alt="Car Brand" />
            <img src="/images_car/7.png" alt="Car Brand" />
            <img src="/images_car/8.png" alt="Car Brand" />
            <img src="/images_car/9.png" alt="Car Brand" />
            <img src="/images_car/10.png" alt="Car Brand" />
            <img src="/images_car/11.png" alt="Car Brand" />
            <!-- تكرار المجموعة للحركة المستمرة -->
            <img src="/images_car/1.png" alt="Car Brand" />
            <img src="/images_car/2.png" alt="Car Brand" />
            <img src="/images_car/3.png" alt="Car Brand" />
            <img src="/images_car/4.png" alt="Car Brand" />
            <img src="/images_car/5.png" alt="Car Brand" />
            <img src="/images_car/6.png" alt="Car Brand" />
            <img src="/images_car/7.png" alt="Car Brand" />
            <img src="/images_car/8.png" alt="Car Brand" />
            <img src="/images_car/9.png" alt="Car Brand" />
            <img src="/images_car/10.png" alt="Car Brand" />
            <img src="/images_car/11.png" alt="Car Brand" />
        </div>
    </div>
</div>
<style>
.brands-marquee-outer {
    display: flex;
    justify-content: center;
    margin-top: 0;
    margin-bottom: 0;
    padding-top: 0;
    padding-bottom: 0;
}
.brands-marquee-wrapper {
    overflow: hidden;
    background: #fff;
    padding: 20px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    width: 80vw;
    max-width: 1200px;
    min-width: 320px;
    margin: 0 0;
    box-sizing: border-box;
    display: flex;
    justify-content: center;
}
.brands-marquee {
    display: flex;
    align-items: center;
    gap: 120px;
    animation: marquee 18s linear infinite;
    width: max-content;
}
.brands-marquee img {
    height: 70px;
    width: auto;
    object-fit: contain;
    filter: grayscale(0.2);
    transition: filter 0.3s;
}
.brands-marquee img:hover {
    filter: grayscale(0) drop-shadow(0 2px 8px #ccc);
}
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
@media (max-width: 900px) {
    .brands-marquee-wrapper {
        width: 96vw;
        max-width: 98vw;
    }
    .brands-marquee {
        gap: 60px;
    }
}
</style>
<!-- LUXURY Cars Section -->
@if(isset($categories['Luxury']) && count($categories['Luxury']) > 0)
<div id="luxury" class="my-5">
    <h2 class="luxury-section-title text-center">LUXURY</h2>
    <div class="row justify-content-center">
        @foreach($categories['Luxury'] as $vehicle)
            <div class="col-12 col-sm-6 col-md-3 mb-4">
                @include('partials.car_card', ['vehicle' => $vehicle])
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- MID RANGE Section -->
@if(isset($categories['Mid-Range']) && count($categories['Mid-Range']) > 0)
<div id="mid-range" class="my-5">
    <h2 class="luxury-section-title text-center">Mid Range</h2>
    <div class="row justify-content-center">
        @foreach($categories['Mid-Range'] as $vehicle)
            <div class="col-12 col-sm-6 col-md-3 mb-4">
                @include('partials.car_card', ['vehicle' => $vehicle])
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- ECONOMY Section -->
@if(isset($categories['Economy']) && count($categories['Economy']) > 0)
<div id="economy" class="my-5">
    <h2 class="luxury-section-title text-center">Economy</h2>
    <div class="row justify-content-center">
        @foreach($categories['Economy'] as $vehicle)
            <div class="col-12 col-sm-6 col-md-3 mb-4">
                @include('partials.car_card', ['vehicle' => $vehicle])
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- SPORTS Section -->
@if(isset($categories['Sports']) && count($categories['Sports']) > 0)
<div id="sports" class="my-5">
    <h2 class="luxury-section-title text-center">Sports</h2>
    <div class="row justify-content-center">
        @foreach($categories['Sports'] as $vehicle)
            <div class="col-12 col-sm-6 col-md-3 mb-4">
                @include('partials.car_card', ['vehicle' => $vehicle])
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- VANS AND BUSES Section -->
@if(isset($categories['Vans']) && count($categories['Vans']) > 0)
<div id="vans" class="my-5">
    <h2 class="luxury-section-title text-center">Vans and Buses</h2>
    <div class="row justify-content-center">
        @foreach($categories['Vans'] as $vehicle)
            <div class="col-12 col-sm-6 col-md-3 mb-4">
                @include('partials.car_card', ['vehicle' => $vehicle])
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- About Section -->
<div id="about" class="my-5">
    <h2 class="luxury-section-title text-center">About Us</h2>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 text-center">
                <p class="lead mb-4" style="font-size: 1.3rem; color: #666; line-height: 1.8;">
                    Welcome to Luxuria UAE, your premier destination for luxury car rentals in the United Arab Emirates.
                    We specialize in providing high-end vehicles for discerning clients who demand excellence in both
                    service and quality.
                </p>
                <p class="mb-4" style="font-size: 1.1rem; color: #666; line-height: 1.7;">
                    With our extensive fleet of premium vehicles, including luxury sedans, sports cars, and SUVs,
                    we ensure that every journey is memorable. Our commitment to customer satisfaction,
                    competitive pricing, and exceptional service makes us the preferred choice for car rentals in the UAE.
                </p>
                <div class="row mt-5">
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-award" style="font-size: 3rem; color: #bfa133;"></i>
                            <h4 class="mt-3">Premium Quality</h4>
                            <p style="color: #666;">Only the finest vehicles in our fleet</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-check" style="font-size: 3rem; color: #bfa133;"></i>
                            <h4 class="mt-3">Safe & Reliable</h4>
                            <p style="color: #666;">All vehicles are regularly maintained</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-headset" style="font-size: 3rem; color: #bfa133;"></i>
                            <h4 class="mt-3">24/7 Support</h4>
                            <p style="color: #666;">Round-the-clock customer service</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div id="contact" class="my-5 pt-5">
    <h2 class="luxury-section-title text-center">Contact Us</h2>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-telephone" style="font-size: 2.5rem; color: #bfa133;"></i>
                                <h5 class="mt-3">Call Us</h5>
                                <p class="mb-2">+971502711549</p>
                                <p class="mb-0">+971542700030</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-envelope" style="font-size: 2.5rem; color: #bfa133;"></i>
                                <h5 class="mt-3">Email Us</h5>
                                <p class="mb-0">info@rentalluxuria.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-geo-alt" style="font-size: 2.5rem; color: #bfa133;"></i>
                                <h5 class="mt-3">Visit Us</h5>
                                <p class="mb-0">Shop No 9 - Dr Murad Building - Hor Al Anz East - Abu Hail - Dubai - UAE</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Coupons Section -->
<div id="promotions" class="my-5">
    <h2 class="luxury-section-title text-center">Exclusive Coupons</h2>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="alert lux-bg-gold fw-bold text-center shadow-sm mb-4" style="font-size:1.2rem;">
                    Get <span style="font-size:1.5rem;">10% off</span> your first booking! Use code <span class="text-uppercase">LUX10</span>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <h5 class="text-success mb-3">Weekend Special</h5>
                                <p class="mb-3">Get 15% off on weekend rentals</p>
                                <span class="badge bg-success fs-6">WEEKEND15</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <h5 class="text-primary mb-3">Monthly Discount</h5>
                                <p class="mb-3">Save 20% on monthly rentals</p>
                                <span class="badge bg-primary fs-6">MONTHLY20</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .luxury-car-card {
        background: linear-gradient(180deg, #666 0%, #fff 80%);
        border-radius: 1.2rem;
        box-shadow: 0 8px 32px 0 rgba(0,0,0,0.18);
        border: 3px solid #fff;
        max-width: 100%;
        min-width: 0;
        overflow: hidden;
        position: relative;
        padding-bottom: 160px;
        height: 500px;
        display: flex;
        flex-direction: column;
    }
    .luxury-car-card .p-4.pb-0 {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        z-index: 1;
    }
    .luxury-card-gradient {
        background: transparent;
        border-bottom-left-radius: 1.2rem;
        border-bottom-right-radius: 1.2rem;
        padding: 1rem 1.2rem 1.2rem 1.2rem !important;
        flex-shrink: 0;
        height: 160px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
    }
    .luxury-car-img {
        max-width: 95%;
        max-height: 360px;
        min-height: 300px;
        object-fit: contain;
        background: transparent;
        margin: 0.5rem auto;
        display: block;
        position: relative;
        z-index: 2;
        flex-shrink: 0;
    }
    .lux-heading.mb-3 {
        margin-bottom: 0.4rem !important;
        font-size: 0.88rem;
    }
    .lux-heading {
        font-size: 0.88rem !important;
    }
    .badge {
        font-size: 0.75rem !important;
        padding: 0.22em 0.7em !important;
    }
    .luxury-card-gradient .fs-5 {
        font-size: 0.88rem !important;
        margin-bottom: 0 !important;
    }
    .luxury-card-gradient .lux-heading {
        font-size: 0.85rem !important;
        margin-bottom: 0.2rem !important;
    }
    .luxury-card-gradient .row {
        margin-bottom: 0.8rem !important;
    }
    .luxury-card-gradient .d-flex {
        margin-top: auto;
    }
    .lux-btn-book {
        background: #aaa;
        color: #fff;
        border-radius: 0.7rem;
        font-size: 0.85rem;
        padding: 0.32rem 1rem;
        border: none;
        transition: background 0.2s;
    }
    .lux-btn-book:hover {
        background: #bfa133;
        color: #fff;
    }
    .lux-whatsapp-icon img {
        filter: drop-shadow(0 2px 6px #2222);
        transition: transform 0.2s;
        width: 36px;
        height: 36px;
    }
    .lux-whatsapp-icon img:hover {
        transform: scale(1.1) rotate(-5deg);
    }
    .luxury-section-title {
        font-size: 80px !important;
        font-weight: 900;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        line-height: 1.05;
        color: #111 !important;
        text-shadow: none;
        margin-bottom: 1.5rem;
    }
    .hero-video-section {
        min-height: 420px;
        height: 60vh;
        max-height: 600px;
        width: 100vw;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        position: relative;
    }
    .hero-bg-video {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;
    }
    .hero-overlay {
        background: rgba(0,0,0,0.35);
        z-index: 2;
    }
    .hero-video-section .container {
        z-index: 3;
        position: relative;
    }
    @media (max-width: 767px) {
        .hero-video-section {
            height: 320px;
            min-height: 220px;
            max-height: 350px;
        }
        .hero-video-section .container {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .hero-video-section h1 {
            font-size: 1.1rem;
        }
    }
    .hero-main-text {
        color: #fff !important;
        text-shadow: 0 2px 8px rgba(0,0,0,0.25);
    }
</style>

<!-- Booking Success Modal -->
<div class="modal fade" id="bookingSuccessModal" tabindex="-1" aria-labelledby="bookingSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; padding: 2rem;">
                <div class="w-100 text-center">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: white;"></i>
                    </div>
                    <h4 class="modal-title mb-0" id="bookingSuccessModalLabel">Booking Confirmed Successfully!</h4>
                    <p class="mb-0 mt-2" style="opacity: 0.9;">Your luxury vehicle has been reserved</p>
                </div>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="booking-success-details" style="background: #f8f9fa; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                    <h6 class="mb-3" style="color: #333;">
                        <i class="bi bi-car-front me-2 text-success"></i>
                        Booking Details
                    </h6>

                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted">Vehicle:</small>
                            <div class="fw-bold" id="successVehicleName">-</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Booking ID:</small>
                            <div class="fw-bold" id="successBookingId">-</div>
                        </div>
							<div class="col-6">
								<small class="text-muted">Reservation UID:</small>
								<div class="fw-bold" id="successExternalUid">-</div>
							</div>
                        <div class="col-6">
                            <small class="text-muted">Start Date:</small>
                            <div class="fw-bold" id="successStartDate">-</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">End Date:</small>
                            <div class="fw-bold" id="successEndDate">-</div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="alert alert-info" style="border-radius: 12px; border: none; background: linear-gradient(135deg, #e3f2fd, #bbdefb);">
                        <i class="bi bi-telephone-fill me-2"></i>
                        <strong>Next Steps:</strong> Our team will contact you shortly to confirm pickup details and finalize your luxury vehicle rental experience.
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border: none; padding: 1rem 2rem 2rem;">
                <button type="button" class="btn btn-success px-4 py-2" data-bs-dismiss="modal" style="border-radius: 12px;">
                    <i class="bi bi-check me-2"></i>Perfect!
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('booking_success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const successData = @json(session('booking_success'));

    // Populate modal with booking data
    document.getElementById('successVehicleName').textContent = successData.vehicle_name;
    document.getElementById('successBookingId').textContent = '#' + successData.booking_id;
	document.getElementById('successExternalUid').textContent = successData.external_reservation_uid || '-';
    document.getElementById('successStartDate').textContent = successData.start_date;
    document.getElementById('successEndDate').textContent = successData.end_date;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('bookingSuccessModal'));
    modal.show();

    // Add confetti effect
    setTimeout(function() {
        // Simple confetti effect using CSS animations
        const confettiContainer = document.createElement('div');
        confettiContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
        `;
        document.body.appendChild(confettiContainer);

        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.style.cssText = `
                position: absolute;
                width: 10px;
                height: 10px;
                background: ${['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57'][Math.floor(Math.random() * 5)]};
                top: -10px;
                left: ${Math.random() * 100}%;
                animation: confetti-fall 3s linear forwards;
                border-radius: 50%;
            `;
            confettiContainer.appendChild(confetti);
        }

        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes confetti-fall {
                to {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Remove confetti after animation
        setTimeout(() => {
            confettiContainer.remove();
            style.remove();
        }, 3000);
    }, 500);
});
</script>
@endif

@if(session('booking_error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    alert('Booking Error: ' + @json(session('booking_error')));
});
</script>
@endif

@endsection
