@extends('layouts.blade_app')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

@section('title', __('app.meta_about_title'))
@section('meta_description', __('app.meta_about_desc'))
@section('canonical_url', url('/about'))

@section('content')
<div class="about-hero-section text-center text-white d-flex align-items-center justify-content-center flex-column">
    <h1 class="display-3 fw-bold mb-3 about-hero-title">{{ __('app.about_hero_title') }}</h1>
    <p class="lead about-hero-desc">{!! __('app.about_hero_desc') !!}</p>
</div>

<div class="container my-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-4 mb-lg-0 {{ $isRtl ? 'order-lg-2' : '' }}">
            <img src="/asset/image.png" alt="Luxuria Cars" class="img-fluid rounded shadow about-main-img" style="border: 6px solid #bfa133;">
        </div>
        <div class="col-lg-6 {{ $isRtl ? 'order-lg-1' : '' }}">
            <h2 class="fw-bold mb-3" style="color:#bfa133;">{{ __('app.about_heading') }}</h2>
            <p class="fs-5" style="color:#222;line-height:1.8;">
                {{ __('app.about_p1') }}
            </p>
            <ul class="list-unstyled fs-5 mt-4">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.about_feat1') }}</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.about_feat2') }}</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.about_feat3') }}</li>
                <li><i class="bi bi-check-circle-fill text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.about_feat4') }}</li>
            </ul>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="about-card p-4 h-100 shadow rounded bg-dark text-white">
                <h3 class="mb-3 text-gold">{{ __('app.about_vision_title') }}</h3>
                <p class="fs-5 mb-0">{{ __('app.about_vision_desc') }}</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="about-card p-4 h-100 shadow rounded bg-white text-dark">
                <h3 class="mb-3 text-gold">{{ __('app.about_mission_title') }}</h3>
                <p class="fs-5 mb-0">{{ __('app.about_mission_desc') }}</p>
            </div>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
            <img src="/asset/image.png" alt="Classic Car Experience" class="img-fluid rounded shadow about-main-img" style="border: 6px solid #bfa133;">
        </div>
        <div class="col-lg-6 order-lg-1">
            <h2 class="fw-bold mb-3" style="color:#bfa133;">{{ __('app.about_why_title') }}</h2>
            <ul class="list-unstyled fs-5">
                <li class="mb-2"><i class="bi bi-star-fill text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.about_why1') }}</li>
                <li class="mb-2"><i class="bi bi-star-fill text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.about_why2') }}</li>
                <li class="mb-2"><i class="bi bi-star-fill text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.about_why3') }}</li>
                <li><i class="bi bi-star-fill text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.about_why4') }}</li>
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
.about-hero-title { font-size: 3.5rem; letter-spacing: {{ $isRtl ? '0' : '0.12em' }}; color: #fff; text-shadow: 0 2px 12px #0008; }
.about-hero-desc  { font-size: 1.5rem; color: #f7e7c1; text-shadow: 0 2px 8px #0005; }
.text-gold  { color: #bfa133 !important; }
.about-main-img { max-width: 100%; min-height: 220px; object-fit: cover; box-shadow: 0 4px 24px 0 rgba(191,161,51,0.12); }
.about-card { border-{{ $isRtl ? 'right' : 'left' }}: 6px solid #bfa133; border-radius: 1.2rem; background: #fff; }
.bg-dark { background: #181818 !important; }
.bg-white { background: #fff !important; }
@media (max-width: 991px) {
    .about-hero-title { font-size: 2.2rem; }
    .about-hero-desc  { font-size: 1.15rem; }
    .about-hero-section { padding: 40px 0 24px 0; min-height: auto; }
}
@media (max-width: 767px) {
    body { overflow-x: hidden; }
    .about-hero-title { font-size: clamp(1.4rem, 6.5vw, 2rem); letter-spacing: {{ $isRtl ? '0' : '0.04em' }}; }
    .about-hero-desc  { font-size: 0.95rem; }
    .about-hero-section { padding: 28px 0 16px 0; min-height: 180px; }
    .about-main-img { min-height: 120px; border: 4px solid #bfa133; }

    /* Body text scale-down */
    .fs-5 { font-size: 0.95rem !important; }
    .about-card { padding: 1.2rem !important; }
    .about-card h3 { font-size: 1rem; }
    .about-card p { font-size: 0.9rem; }

    /* Section headings */
    h2[style*="color:#bfa133"] { font-size: 1.35rem !important; }

    /* Why choose us list */
    .list-unstyled.fs-5 li { font-size: 0.9rem !important; margin-bottom: 0.5rem; }
    .list-unstyled.fs-5 i { font-size: 0.9rem; }
}
@media (max-width: 575px) {
    .about-hero-title { font-size: clamp(1.2rem, 6vw, 1.6rem); }
    .about-hero-desc  { font-size: 0.88rem; }
    .about-hero-section { padding: 22px 0 14px 0; min-height: 160px; }
    .container.my-5 { padding-left: 0.85rem; padding-right: 0.85rem; }
    .about-card { padding: 1rem !important; }
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
