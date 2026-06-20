@extends('layouts.blade_app')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

@section('title', __('app.meta_contact_title'))
@section('meta_description', __('app.meta_contact_desc'))
@section('canonical_url', url('/contact'))

@section('content')
<div class="contact-hero-section text-center text-white d-flex align-items-center justify-content-center flex-column">
    <h1 class="display-3 fw-bold mb-3 contact-hero-title">{{ __('app.contact_hero_title') }}</h1>
    <p class="lead contact-hero-desc">{!! __('app.contact_hero_desc') !!}</p>
</div>

<div class="container my-5">
    <div class="row mb-5">
        <div class="col-lg-4 mb-4">
            <div class="contact-card h-100 p-4 text-center shadow rounded">
                <div class="contact-icon mb-3"><i class="bi bi-telephone-fill"></i></div>
                <h2 class="text-gold mb-3">{{ __('app.contact_call_title') }}</h2>
                <p class="fs-5 mb-2">+971 50 271 1549</p>
                <p class="fs-5 mb-0">+971 54 270 0030</p>
                <small class="text-muted">{{ __('app.contact_available') }}</small>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="contact-card h-100 p-4 text-center shadow rounded">
                <div class="contact-icon mb-3"><i class="bi bi-envelope-fill"></i></div>
                <h2 class="text-gold mb-3">{{ __('app.contact_email_title') }}</h2>
                <p class="fs-5 mb-2">info@rentalluxuria.com</p>
                <p class="fs-5 mb-0">bookings@rentalluxuria.com</p>
                <small class="text-muted">{{ __('app.contact_reply') }}</small>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="contact-card h-100 p-4 text-center shadow rounded">
                <div class="contact-icon mb-3"><i class="bi bi-geo-alt-fill"></i></div>
                <h2 class="text-gold mb-3">{{ __('app.contact_visit_title') }}</h2>
                <p class="fs-6 mb-0">{!! __('app.contact_address') !!}</p>
                <small class="text-muted">{{ __('app.contact_open') }}</small>
            </div>
        </div>
    </div>

    <div class="row align-items-start">
        <div class="col-lg-8 mb-4">
            <div class="contact-form-card p-4 shadow rounded">
                <h2 class="text-gold mb-4" style="font-size:1.4rem;">{{ __('app.contact_form_title') }}</h2>
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">{{ __('app.contact_first_name') }}</label>
                            <input type="text" class="form-control contact-input" id="firstName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">{{ __('app.contact_last_name') }}</label>
                            <input type="text" class="form-control contact-input" id="lastName" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">{{ __('app.contact_email') }}</label>
                            <input type="email" class="form-control contact-input" id="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">{{ __('app.contact_phone') }}</label>
                            <input type="tel" class="form-control contact-input" id="phone" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="service" class="form-label">{{ __('app.contact_service') }}</label>
                        <select class="form-select contact-input" id="service">
                            <option>{{ __('app.contact_service_luxury') }}</option>
                            <option>{{ __('app.contact_service_chauffeur') }}</option>
                            <option>{{ __('app.contact_service_event') }}</option>
                            <option>{{ __('app.contact_service_corp') }}</option>
                            <option>{{ __('app.contact_service_other') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">{{ __('app.contact_message') }}</label>
                        <textarea class="form-control contact-input" id="message" rows="4" placeholder="{{ __('app.contact_placeholder') }}"></textarea>
                    </div>
                    <button type="submit" class="btn contact-btn-submit px-4 py-2">{{ __('app.contact_send') }}</button>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            {{-- Visually-hidden H2 provides a semantic parent for the H3 sidebar blocks --}}
            <h2 class="visually-hidden">{{ $isRtl ? 'معلومات إضافية' : 'Additional Contact Information' }}</h2>
            <div class="contact-hours-card p-4 shadow rounded mb-4">
                <h3 class="text-gold mb-3">{{ __('app.contact_hours_title') }}</h3>
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>{{ __('app.contact_mon_fri') }}</strong> 8:00 AM - 10:00 PM</li>
                    <li class="mb-2"><strong>{{ __('app.contact_sat') }}</strong> 8:00 AM - 11:00 PM</li>
                    <li class="mb-2"><strong>{{ __('app.contact_sun') }}</strong> 9:00 AM - 9:00 PM</li>
                    <li class="text-gold"><strong>{{ __('app.contact_emergency') }}</strong> {{ __('app.contact_24_7') }}</li>
                </ul>
            </div>
            <div class="contact-services-card p-4 shadow rounded">
                <h3 class="text-gold mb-3">{{ __('app.contact_services_title') }}</h3>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.contact_svc1') }}</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.contact_svc2') }}</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.contact_svc3') }}</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.contact_svc4') }}</li>
                    <li><i class="bi bi-check-circle text-gold {{ $isRtl ? 'ms-2' : 'me-2' }}"></i> {{ __('app.contact_svc5') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12 text-center">
            <div class="whatsapp-contact-section p-4 rounded shadow">
                <h2 class="text-gold mb-3" style="font-size:1.4rem;">{{ __('app.contact_wa_title') }}</h2>
                <p class="fs-5 mb-3">{{ __('app.contact_wa_desc') }}</p>
                <a href="https://wa.me/971502711549?text=Hi%20Luxuria%20Cars,%20I'm%20interested%20in%20your%20services" target="_blank" class="btn whatsapp-btn px-4 py-2">
                    <i class="bi bi-whatsapp {{ $isRtl ? 'ms-2' : 'me-2' }}"></i>{{ __('app.contact_wa_btn') }}
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.contact-hero-section { min-height:300px; background:linear-gradient(135deg,#1a1a1a 0%,#bfa133 100%); padding:60px 0 40px; box-shadow:0 8px 32px 0 rgba(0,0,0,0.18); }
.contact-hero-title  { font-size:3.5rem; letter-spacing:{{ $isRtl ? '0' : '0.12em' }}; color:#fff; text-shadow:0 2px 12px #0008; }
.contact-hero-desc   { font-size:1.3rem; color:#f7e7c1; text-shadow:0 2px 8px #0005; }
.text-gold { color:#bfa133 !important; }
.contact-card { background:#fff; border:1px solid #f0f0f0; border-top:4px solid #bfa133; transition:transform 0.3s,box-shadow 0.3s; }
.contact-card:hover { transform:translateY(-5px); box-shadow:0 12px 40px rgba(191,161,51,0.15) !important; }
.contact-icon { font-size:2.5rem; color:#bfa133; }
.contact-form-card,
.contact-hours-card,
.contact-services-card { background:#fff; border:1px solid #f0f0f0; border-{{ $isRtl ? 'right' : 'left' }}:6px solid #bfa133; }
.contact-input { border:2px solid #e9ecef; border-radius:0.5rem; padding:0.75rem 1rem; transition:border-color 0.3s; }
.contact-input:focus { border-color:#bfa133; box-shadow:0 0 0 0.2rem rgba(191,161,51,0.25); }
.contact-btn-submit { background:#bfa133; color:#fff; border:none; border-radius:0.5rem; font-weight:600; transition:background 0.3s; }
.contact-btn-submit:hover { background:#a88c2c; color:#fff; }
.whatsapp-contact-section { background:linear-gradient(45deg,#f8f9fa,#e9ecef); border:2px solid #bfa133; }
.whatsapp-btn { background:#25d366; color:#fff; border:none; border-radius:50px; font-weight:600; transition:background 0.3s; }
.whatsapp-btn:hover { background:#1da851; color:#fff; }
@media (max-width:991px) {
    .contact-hero-title { font-size:2.5rem; }
    .contact-hero-section { padding:40px 0 24px; }
    .contact-hero-desc { font-size:1.1rem; }
}
@media (max-width:767px) {
    body { overflow-x:hidden; }
    .contact-hero-title { font-size:clamp(1.6rem,7vw,2rem); letter-spacing:0.04em; }
    .contact-hero-desc { font-size:0.95rem; }
    .contact-hero-section { padding:28px 0 18px; min-height:200px; }

    /* Contact cards */
    .contact-card { padding:1.2rem !important; }
    .contact-icon { font-size:2rem; }
    .contact-card h2 { font-size:1rem; }
    .contact-card p, .contact-card small { font-size:0.9rem; word-break:break-word; }

    /* Form area */
    .contact-form-card { padding:1.25rem !important; }
    .contact-form-card h2 { font-size:1.1rem; }
    .contact-input { padding:0.6rem 0.9rem; font-size:0.9rem; }
    .contact-btn-submit { width:100%; padding:0.7rem; font-size:0.9rem; min-height:44px; }

    /* Sidebar */
    .contact-hours-card, .contact-services-card { padding:1rem !important; }
    .contact-hours-card h3, .contact-services-card h3 { font-size:1rem; }
    .contact-hours-card li, .contact-services-card li { font-size:0.88rem; }

    /* WhatsApp CTA */
    .whatsapp-contact-section { padding:1.25rem !important; }
    .whatsapp-contact-section h2 { font-size:1.1rem; }
    .whatsapp-contact-section p { font-size:0.9rem; }
    .whatsapp-btn { width:100%; padding:0.7rem 1rem; font-size:0.9rem; min-height:44px; }
}
@media (max-width:575px) {
    .contact-hero-title { font-size:clamp(1.3rem,6.5vw,1.7rem); }
    .contact-hero-section { padding:24px 0 16px; min-height:170px; }
    .contact-hero-desc { font-size:0.88rem; }
    .contact-card { padding:1rem !important; }
    .contact-card p { font-size:0.85rem; }
    .container.my-5 { padding-left:0.85rem; padding-right:0.85rem; }
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
