@extends('layouts.blade_app')

@section('title', 'About Us - LUXURIA CARS RENTAL')
@section('meta_description', 'Learn about Luxuria Cars Rental - UAE premier destination for luxury and classic car rentals. Discover our legacy of excellence and commitment to bespoke experiences.')

@section('content')
<div class="about-hero-section text-center text-white d-flex align-items-center justify-content-center flex-column">
    <h1 class="display-3 fw-bold mb-3 about-hero-title">LUXURIA CARS RENTAL</h1>
    <p class="lead about-hero-desc">Luxury. Heritage. Excellence.<br>Where every journey is a statement.</p>
</div>

<div class="container my-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="/asset/image.png" alt="Luxuria Cars" class="img-fluid rounded shadow about-main-img" style="border: 6px solid #bfa133;">
        </div>
        <div class="col-lg-6">
            <h2 class="fw-bold mb-3" style="color:#bfa133;">About Luxuria</h2>
            <p class="fs-5" style="color:#222;line-height:1.8;">
                Luxuria Cars Rental is the UAE’s premier destination for luxury and classic car rentals. With a legacy of excellence, we offer an exclusive fleet of the world’s most prestigious vehicles, blending timeless elegance with modern comfort. Our commitment is to provide a seamless, bespoke experience for every client, whether for business, leisure, or special occasions.
            </p>
            <ul class="list-unstyled fs-5 mt-4">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-gold me-2"></i> Handpicked luxury & classic cars</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-gold me-2"></i> White-glove customer service</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-gold me-2"></i> Chauffeur & VIP services</li>
                <li><i class="bi bi-check-circle-fill text-gold me-2"></i> Discreet, reliable, and always on time</li>
            </ul>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="about-card p-4 h-100 shadow rounded bg-dark text-white">
                <h3 class="mb-3 text-gold">Our Vision</h3>
                <p class="fs-5 mb-0">To redefine luxury mobility in the UAE by delivering unforgettable experiences and setting the benchmark for excellence in car rental services.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="about-card p-4 h-100 shadow rounded bg-white text-dark">
                <h3 class="mb-3 text-gold">Our Mission</h3>
                <p class="fs-5 mb-0">To provide our clients with a curated selection of the world’s finest vehicles, exceptional service, and absolute peace of mind—every mile, every time.</p>
            </div>
        </div>
    </div>
    <div class="row align-items-center">
        <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
            <img src="/asset/image.png" alt="Classic Car Experience" class="img-fluid rounded shadow about-main-img" style="border: 6px solid #bfa133;">
        </div>
        <div class="col-lg-6 order-lg-1">
            <h2 class="fw-bold mb-3" style="color:#bfa133;">Why Choose Us?</h2>
            <ul class="list-unstyled fs-5">
                <li class="mb-2"><i class="bi bi-star-fill text-gold me-2"></i> Impeccable fleet of luxury & classic cars</li>
                <li class="mb-2"><i class="bi bi-star-fill text-gold me-2"></i> Personalized, 24/7 concierge support</li>
                <li class="mb-2"><i class="bi bi-star-fill text-gold me-2"></i> Transparent pricing, no hidden fees</li>
                <li><i class="bi bi-star-fill text-gold me-2"></i> Trusted by celebrities, executives, and car enthusiasts</li>
            </ul>
        </div>
    </div>
</div>

<style>
.about-hero-section {
    min-height: 340px;
    background: linear-gradient(120deg, #111 60%, #bfa133 100%);
    padding: 60px 0 40px 0;
    box-shadow: 0 8px 32px 0 rgba(0,0,0,0.18);
    position: relative;
}
.about-hero-title {
    font-size: 3.5rem;
    letter-spacing: 0.12em;
    color: #fff;
    text-shadow: 0 2px 12px #0008;
}
.about-hero-desc {
    font-size: 1.5rem;
    color: #f7e7c1;
    text-shadow: 0 2px 8px #0005;
}
.text-gold {
    color: #bfa133 !important;
}
.about-main-img {
    max-width: 100%;
    min-height: 220px;
    object-fit: cover;
    box-shadow: 0 4px 24px 0 rgba(191,161,51,0.12);
}
.about-card {
    border-left: 6px solid #bfa133;
    border-radius: 1.2rem;
    background: #fff;
}
.bg-dark {
    background: #181818 !important;
}
.bg-white {
    background: #fff !important;
}
@media (max-width: 991px) {
    .about-hero-title {
        font-size: 2.2rem;
    }
    .about-hero-section {
        padding: 40px 0 24px 0;
    }
}
@media (max-width: 767px) {
    .about-hero-title {
        font-size: 1.4rem;
    }
    .about-hero-section {
        padding: 24px 0 12px 0;
    }
    .about-main-img {
        min-height: 120px;
    }
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
