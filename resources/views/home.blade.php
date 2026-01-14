@extends('layouts.blade_app')

@section('title', 'Welcome to Luxuria UAE')
@section('meta_description', 'Luxuria UAE - Premium luxury car rental in Dubai. Rent luxury, sports, classic, and economy vehicles with exceptional service.')

@section('content')
<!-- Hero Section -->
<div class="hero-section position-relative overflow-hidden">
    <div class="hero-background"></div>
    <div class="hero-pattern"></div>
    <div class="container h-100 position-relative z-2">
        <div class="row align-items-center justify-content-center h-100" style="min-height: 600px;">
            <div class="col-12 col-lg-10 col-xl-9">
                <h1 class="hero-main-title mb-4 text-center">Premium Luxury Car Rental</h1>
                <p class="hero-description mb-5 text-center">Experience the epitome of elegance and sophistication.<br>Drive the world's most prestigious vehicles in the heart of the United Arab Emirates.</p>
                <div class="vehicle-filter-wrapper">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="filter-label">Make</label>
                            <select id="filter-make" class="form-select filter-select">
                                <option value="">Select Make</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="filter-label">Model</label>
                            <select id="filter-model" class="form-select filter-select" disabled>
                                <option value="">Select Model</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="filter-label">Year</label>
                            <select id="filter-year" class="form-select filter-select" disabled>
                                <option value="">Select Year</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button id="filter-search-btn" class="btn btn-hero-primary w-100 py-3" disabled>
                                <i class="bi bi-search me-2"></i>Search Vehicles
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
    <h1 class="luxury-section-title text-center">LUXURY</h1>
    <div class="row justify-content-center">
        @foreach($categories['Luxury'] as $vehicle)
            <div class="col-12 col-sm-6 col-md-3 mb-4 vehicle-card" 
                 data-make="{{ strtolower($vehicle['name'] ?? '') }}" 
                 data-model="{{ strtolower($vehicle['model'] ?? '') }}" 
                 data-year="{{ $vehicle['year'] ?? '' }}"
                 data-category="luxury">
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
            <div class="col-12 col-sm-6 col-md-3 mb-4 vehicle-card" 
                 data-make="{{ strtolower($vehicle['name'] ?? '') }}" 
                 data-model="{{ strtolower($vehicle['model'] ?? '') }}" 
                 data-year="{{ $vehicle['year'] ?? '' }}"
                 data-category="mid-range">
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
            <div class="col-12 col-sm-6 col-md-3 mb-4 vehicle-card" 
                 data-make="{{ strtolower($vehicle['name'] ?? '') }}" 
                 data-model="{{ strtolower($vehicle['model'] ?? '') }}" 
                 data-year="{{ $vehicle['year'] ?? '' }}"
                 data-category="economy">
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
            <div class="col-12 col-sm-6 col-md-3 mb-4 vehicle-card" 
                 data-make="{{ strtolower($vehicle['name'] ?? '') }}" 
                 data-model="{{ strtolower($vehicle['model'] ?? '') }}" 
                 data-year="{{ $vehicle['year'] ?? '' }}"
                 data-category="sports">
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
            <div class="col-12 col-sm-6 col-md-3 mb-4 vehicle-card" 
                 data-make="{{ strtolower($vehicle['name'] ?? '') }}" 
                 data-model="{{ strtolower($vehicle['model'] ?? '') }}" 
                 data-year="{{ $vehicle['year'] ?? '' }}"
                 data-category="vans">
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
                            <h3 class="mt-3">Premium Quality</h3>
                            <p style="color: #666;">Only the finest vehicles in our fleet</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-check" style="font-size: 3rem; color: #bfa133;"></i>
                            <h3 class="mt-3">Safe & Reliable</h3>
                            <p style="color: #666;">All vehicles are regularly maintained</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-headset" style="font-size: 3rem; color: #bfa133;"></i>
                            <h3 class="mt-3">24/7 Support</h3>
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
                                <h3 class="mt-3">Call Us</h3>
                                <p class="mb-2">+971502711549</p>
                                <p class="mb-0">+971542700030</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-envelope" style="font-size: 2.5rem; color: #bfa133;"></i>
                                <h3 class="mt-3">Email Us</h3>
                                <p class="mb-0">info@rentalluxuria.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-geo-alt" style="font-size: 2.5rem; color: #bfa133;"></i>
                                <h3 class="mt-3">Visit Us</h3>
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
                                <h3 class="text-success mb-3">Weekend Special</h3>
                                <p class="mb-3">Get 15% off on weekend rentals</p>
                                <span class="badge bg-success fs-6">WEEKEND15</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <h3 class="text-primary mb-3">Monthly Discount</h3>
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
    .hero-section {
        min-height: 600px;
        height: 85vh;
        max-height: 800px;
        width: 100vw;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 30%, #ffffff 70%, #f5f5f5 100%);
        z-index: 1;
    }
    .hero-pattern {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(circle at 25% 25%, rgba(191, 161, 51, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 75% 75%, rgba(191, 161, 51, 0.03) 0%, transparent 50%),
            linear-gradient(45deg, transparent 30%, rgba(191, 161, 51, 0.02) 50%, transparent 70%);
        z-index: 1;
    }
    .hero-pattern::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg width="60" height="60" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="dots" width="60" height="60" patternUnits="userSpaceOnUse"><circle cx="30" cy="30" r="1" fill="rgba(191,161,51,0.08)"/></pattern></defs><rect width="60" height="60" fill="url(%23dots)"/></svg>');
        opacity: 0.3;
    }
    .hero-section .container {
        z-index: 2;
        position: relative;
    }
    .hero-main-title {
        font-size: 4rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: #1a1a1a;
        text-shadow: 
            0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        line-height: 1.2;
        animation: fadeInUp 1s ease-out;
    }
    .hero-description {
        font-size: 1.15rem;
        font-weight: 300;
        line-height: 1.9;
        color: #333333;
        max-width: 900px;
        margin: 0 auto 2rem;
        animation: fadeInUp 1.4s ease-out;
    }
    .hero-buttons {
        animation: fadeInUp 1.6s ease-out;
    }
    .vehicle-filter-wrapper {
        animation: fadeInUp 1.6s ease-out;
        background: rgba(255, 255, 255, 0.95);
        padding: 2.5rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(191, 161, 51, 0.2);
        max-width: 100%;
        margin: 0 auto;
    }
    .vehicle-card {
        transition: opacity 0.3s ease;
    }
    #reset-search-btn {
        display: block;
        margin: 20px auto 0;
    }
    .filter-label {
        display: block;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .filter-select {
        height: 50px;
        border: 2px solid rgba(191, 161, 51, 0.3);
        border-radius: 10px;
        font-size: 1rem;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        background: #fff;
    }
    .filter-select:focus {
        border-color: #bfa133;
        box-shadow: 0 0 0 0.2rem rgba(191, 161, 51, 0.25);
        outline: none;
    }
    .filter-select:disabled {
        background: #f5f5f5;
        cursor: not-allowed;
        opacity: 0.6;
    }
    #filter-search-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .btn-hero-primary {
        background: linear-gradient(135deg, #bfa133 0%, #d4b845 50%, #bfa133 100%);
        background-size: 200% 200%;
        color: #000;
        border: none;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 1rem 2.5rem;
        transition: all 0.4s ease;
        box-shadow: 
            0 6px 25px rgba(191, 161, 51, 0.5),
            0 0 20px rgba(191, 161, 51, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
    }
    .btn-hero-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }
    .btn-hero-primary:hover::before {
        left: 100%;
    }
    .btn-hero-primary:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 8px 35px rgba(191, 161, 51, 0.7),
            0 0 30px rgba(191, 161, 51, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        background-position: right center;
    }
    .btn-hero-secondary {
        background: transparent;
        color: #bfa133;
        border: 2px solid #bfa133;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 1rem 2.5rem;
        transition: all 0.4s ease;
        box-shadow: 0 2px 10px rgba(191, 161, 51, 0.2);
    }
    .btn-hero-secondary:hover {
        background: #bfa133;
        border-color: #bfa133;
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 
            0 6px 25px rgba(191, 161, 51, 0.5),
            0 0 20px rgba(191, 161, 51, 0.3);
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @media (max-width: 991px) {
        .hero-main-title {
            font-size: 3rem;
        }
        .hero-description {
            font-size: 1.05rem;
        }
    }
    @media (max-width: 767px) {
        .hero-section {
            min-height: 550px;
            height: auto;
            padding: 2rem 0;
        }
        .hero-main-title {
            font-size: 2rem;
            letter-spacing: 0.1em;
        }
        .hero-description {
            font-size: 0.95rem;
            line-height: 1.7;
        }
        .hero-buttons {
            flex-direction: column;
            align-items: center;
        }
        .btn-hero-primary,
        .btn-hero-secondary {
            width: 100%;
            max-width: 280px;
            padding: 0.9rem 2rem;
            font-size: 0.9rem;
        }
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
                    <h2 class="modal-title mb-0" id="bookingSuccessModalLabel">Booking Confirmed Successfully!</h2>
                    <p class="mb-0 mt-2" style="opacity: 0.9;">Your luxury vehicle has been reserved</p>
                </div>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="booking-success-details" style="background: #f8f9fa; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                    <h3 class="mb-3" style="color: #333;">
                        <i class="bi bi-car-front me-2 text-success"></i>
                        Booking Details
                    </h3>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const makeSelect = document.getElementById('filter-make');
    const modelSelect = document.getElementById('filter-model');
    const yearSelect = document.getElementById('filter-year');
    const searchBtn = document.getElementById('filter-search-btn');

    if (!makeSelect || !modelSelect || !yearSelect || !searchBtn) {
        return; // Elements not found
    }

    // Load makes on page load
    fetch('/api/vehicles/makes')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.makes) {
                data.makes.forEach(make => {
                    const option = document.createElement('option');
                    option.value = make;
                    option.textContent = make;
                    makeSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading makes:', error);
        });

    // When make is selected, load models
    makeSelect.addEventListener('change', function() {
        const selectedMake = this.value;
        
        // Reset model and year
        modelSelect.innerHTML = '<option value="">Select Model</option>';
        modelSelect.disabled = !selectedMake;
        yearSelect.innerHTML = '<option value="">Select Year</option>';
        yearSelect.disabled = true;
        searchBtn.disabled = true;

        if (selectedMake) {
            fetch(`/api/vehicles/models?make=${encodeURIComponent(selectedMake)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.models) {
                        data.models.forEach(model => {
                            const option = document.createElement('option');
                            option.value = model;
                            option.textContent = model;
                            modelSelect.appendChild(option);
                        });
                        modelSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error loading models:', error);
                });
        }
    });

    // When model is selected, load years
    modelSelect.addEventListener('change', function() {
        const selectedMake = makeSelect.value;
        const selectedModel = this.value;
        
        // Reset year
        yearSelect.innerHTML = '<option value="">Select Year</option>';
        yearSelect.disabled = !selectedModel;
        searchBtn.disabled = true;

        if (selectedMake && selectedModel) {
            fetch(`/api/vehicles/years?make=${encodeURIComponent(selectedMake)}&model=${encodeURIComponent(selectedModel)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.years) {
                        data.years.forEach(year => {
                            const option = document.createElement('option');
                            option.value = year;
                            option.textContent = year;
                            yearSelect.appendChild(option);
                        });
                        yearSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error loading years:', error);
                });
        }
    });

    // When year is selected, enable search button
    yearSelect.addEventListener('change', function() {
        searchBtn.disabled = !this.value;
    });

    // Search button click
    searchBtn.addEventListener('click', function() {
        const make = makeSelect.value.toLowerCase().trim();
        const model = modelSelect.value.toLowerCase().trim();
        const year = yearSelect.value.trim();

        if (!make || !model || !year) {
            alert('Please select Make, Model, and Year');
            return;
        }

        // Hide all vehicle cards
        const allVehicleCards = document.querySelectorAll('.vehicle-card');
        allVehicleCards.forEach(card => {
            card.style.display = 'none';
        });

        // Hide all category sections initially
        const allSections = document.querySelectorAll('#luxury, #mid-range, #economy, #sports, #vans');
        allSections.forEach(section => {
            section.style.display = 'none';
        });

        // Find matching vehicle
        let foundVehicle = null;
        let foundCategory = null;

        allVehicleCards.forEach(card => {
            const cardMake = (card.getAttribute('data-make') || '').toLowerCase().trim();
            const cardModel = (card.getAttribute('data-model') || '').toLowerCase().trim();
            const cardYear = String(card.getAttribute('data-year') || '').trim();

            // Normalize comparison - remove extra spaces and compare
            const normalizedCardMake = cardMake.replace(/\s+/g, ' ').trim();
            const normalizedCardModel = cardModel.replace(/\s+/g, ' ').trim();
            const normalizedMake = make.replace(/\s+/g, ' ').trim();
            const normalizedModel = model.replace(/\s+/g, ' ').trim();

            if (normalizedCardMake === normalizedMake && 
                normalizedCardModel === normalizedModel && 
                cardYear === year) {
                foundVehicle = card;
                foundCategory = card.getAttribute('data-category');
            }
        });

        if (foundVehicle && foundCategory) {
            // Show the matching vehicle
            foundVehicle.style.display = 'block';

            // Show the category section
            const categorySectionId = foundCategory === 'mid-range' ? 'mid-range' : foundCategory;
            const categorySection = document.getElementById(categorySectionId);
            if (categorySection) {
                categorySection.style.display = 'block';
                
                // Scroll to the category section smoothly
                setTimeout(() => {
                    categorySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }

            // Add reset button if not exists
            if (!document.getElementById('reset-search-btn')) {
                const resetBtn = document.createElement('button');
                resetBtn.id = 'reset-search-btn';
                resetBtn.className = 'btn btn-hero-secondary mt-3';
                resetBtn.innerHTML = '<i class="bi bi-arrow-counterclockwise me-2"></i>Show All Vehicles';
                resetBtn.style.display = 'block';
                resetBtn.style.margin = '20px auto';
                resetBtn.onclick = function() {
                    // Reset all filters
                    makeSelect.value = '';
                    modelSelect.value = '';
                    yearSelect.value = '';
                    modelSelect.disabled = true;
                    yearSelect.disabled = true;
                    searchBtn.disabled = true;

                    // Show all vehicles
                    allVehicleCards.forEach(card => {
                        card.style.display = 'block';
                    });

                    // Show all sections
                    allSections.forEach(section => {
                        section.style.display = 'block';
                    });

                    // Remove reset button
                    resetBtn.remove();

                    // Scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                };
                document.querySelector('.vehicle-filter-wrapper').appendChild(resetBtn);
            }
        } else {
            // No vehicle found
            alert('No vehicle found matching your search criteria.');
            
            // Show all vehicles again
            allVehicleCards.forEach(card => {
                card.style.display = 'block';
            });
            allSections.forEach(section => {
                section.style.display = 'block';
            });
        }
    });
});
</script>

@endsection
