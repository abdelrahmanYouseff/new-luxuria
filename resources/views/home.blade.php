@extends('layouts.blade_app')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

@section('title', __('app.meta_home_title'))
@section('meta_description', __('app.meta_home_desc'))
@section('canonical_url', url('/'))
@section('og_type', 'website')

@section('content')
<!-- Hero Section -->
<div class="hero-section position-relative overflow-hidden">
    <div class="hero-background"></div>
    <div class="hero-pattern"></div>
    <div class="container h-100 position-relative z-2">
        <div class="row align-items-center justify-content-center h-100" style="min-height: 600px;">
            <div class="col-12 col-lg-10 col-xl-9">
                <h1 class="hero-main-title mb-4 text-center">{{ __('app.hero_title') }}</h1>
                <p class="hero-description mb-5 text-center">{!! __('app.hero_description') !!}</p>
                <div class="vehicle-filter-wrapper">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="filter-label">{{ __('app.hero_make') }}</label>
                            <select id="filter-make" class="form-select filter-select">
                                <option value="">{{ __('app.hero_select_make') }}</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="filter-label">{{ __('app.hero_model') }}</label>
                            <select id="filter-model" class="form-select filter-select" disabled>
                                <option value="">{{ __('app.hero_select_model') }}</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="filter-label">{{ __('app.hero_year') }}</label>
                            <select id="filter-year" class="form-select filter-select" disabled>
                                <option value="">{{ __('app.hero_select_year') }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button id="filter-search-btn" class="btn btn-hero-primary w-100 py-3" disabled>
                                <i class="bi bi-search me-2"></i>{{ __('app.hero_search_btn') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Brand Marquee -->
<div class="brands-marquee-outer">
    <div class="brands-marquee-wrapper">
        <div class="brands-marquee">
            <img src="/images_car/1.png" alt="Car Brand" /><img src="/images_car/2.png" alt="Car Brand" />
            <img src="/images_car/3.png" alt="Car Brand" /><img src="/images_car/4.png" alt="Car Brand" />
            <img src="/images_car/5.png" alt="Car Brand" /><img src="/images_car/6.png" alt="Car Brand" />
            <img src="/images_car/7.png" alt="Car Brand" /><img src="/images_car/8.png" alt="Car Brand" />
            <img src="/images_car/9.png" alt="Car Brand" /><img src="/images_car/10.png" alt="Car Brand" />
            <img src="/images_car/11.png" alt="Car Brand" />
            <img src="/images_car/1.png" alt="Car Brand" /><img src="/images_car/2.png" alt="Car Brand" />
            <img src="/images_car/3.png" alt="Car Brand" /><img src="/images_car/4.png" alt="Car Brand" />
            <img src="/images_car/5.png" alt="Car Brand" /><img src="/images_car/6.png" alt="Car Brand" />
            <img src="/images_car/7.png" alt="Car Brand" /><img src="/images_car/8.png" alt="Car Brand" />
            <img src="/images_car/9.png" alt="Car Brand" /><img src="/images_car/10.png" alt="Car Brand" />
            <img src="/images_car/11.png" alt="Car Brand" />
        </div>
    </div>
</div>

<style>
.brands-marquee-outer { width:100%; overflow:hidden; margin:0; padding:0; }
.brands-marquee-wrapper { overflow:hidden; background:#fff; padding:20px 0; border-top:1px solid #eee; border-bottom:1px solid #eee; width:100%; box-sizing:border-box; }
.brands-marquee { display:flex; align-items:center; gap:80px; animation:marquee 18s linear infinite; width:max-content; }
.brands-marquee img { height:60px; width:auto; object-fit:contain; filter:grayscale(0.2); transition:filter 0.3s; flex-shrink:0; }
.brands-marquee img:hover { filter:grayscale(0) drop-shadow(0 2px 8px #ccc); }
@keyframes marquee { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
@media (max-width:767px) { .brands-marquee-wrapper{ padding:10px 0; } .brands-marquee{ gap:40px; animation-duration:22s; } .brands-marquee img{ height:36px; } }
</style>

{{-- Visually-hidden H2 groups the entire fleet section for screen readers & SEO --}}
<h2 class="visually-hidden">{{ $isRtl ? 'أسطول السيارات المتاحة للإيجار' : 'Our Rental Fleet – Browse All Vehicles' }}</h2>

<!-- LUXURY Cars Section -->
@if(isset($categories['Luxury']) && count($categories['Luxury']) > 0)
<div id="luxury" class="my-5">
    <h2 class="luxury-section-title text-center">{{ __('app.cat_luxury') }}</h2>
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
    <h2 class="luxury-section-title text-center">{{ __('app.cat_mid_range') }}</h2>
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
    <h2 class="luxury-section-title text-center">{{ __('app.cat_economy') }}</h2>
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
    <h2 class="luxury-section-title text-center">{{ __('app.cat_sports') }}</h2>
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
    <h2 class="luxury-section-title text-center">{{ __('app.cat_vans') }}</h2>
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
    <h2 class="luxury-section-title text-center">{{ __('app.home_about_title') }}</h2>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 text-center">
                <p class="lead mb-4" style="font-size: 1.3rem; color: #666; line-height: 1.8;">
                    {{ __('app.home_about_p1') }}
                </p>
                <p class="mb-4" style="font-size: 1.1rem; color: #666; line-height: 1.7;">
                    {{ __('app.home_about_p2') }}
                </p>
                <div class="row mt-5">
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-award" style="font-size: 3rem; color: #bfa133;"></i>
                            <h3 class="mt-3">{{ __('app.home_about_feat1') }}</h3>
                            <p style="color: #666;">{{ __('app.home_about_feat1_d') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-check" style="font-size: 3rem; color: #bfa133;"></i>
                            <h3 class="mt-3">{{ __('app.home_about_feat2') }}</h3>
                            <p style="color: #666;">{{ __('app.home_about_feat2_d') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-headset" style="font-size: 3rem; color: #bfa133;"></i>
                            <h3 class="mt-3">{{ __('app.home_about_feat3') }}</h3>
                            <p style="color: #666;">{{ __('app.home_about_feat3_d') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div id="contact" class="my-5 pt-5">
    <h2 class="luxury-section-title text-center">{{ __('app.home_contact_title') }}</h2>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-telephone" style="font-size: 2.5rem; color: #bfa133;"></i>
                                <h3 class="mt-3">{{ __('app.home_contact_call') }}</h3>
                                <p class="mb-2">+971502711549</p>
                                <p class="mb-0">+971542700030</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-envelope" style="font-size: 2.5rem; color: #bfa133;"></i>
                                <h3 class="mt-3">{{ __('app.home_contact_email') }}</h3>
                                <p class="mb-0">info@rentalluxuria.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-geo-alt" style="font-size: 2.5rem; color: #bfa133;"></i>
                                <h3 class="mt-3">{{ __('app.home_contact_visit') }}</h3>
                                <p class="mb-0">{{ __('app.home_address') }}</p>
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
    <h2 class="luxury-section-title text-center">{{ __('app.home_coupons_title') }}</h2>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="alert lux-bg-gold fw-bold text-center shadow-sm mb-4" style="font-size:1.2rem;">
                    {!! __('app.home_coupons_promo') !!}
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <h3 class="text-success mb-3">{{ __('app.home_coupon1_title') }}</h3>
                                <p class="mb-3">{{ __('app.home_coupon1_desc') }}</p>
                                <span class="badge bg-success fs-6">WEEKEND15</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <h3 class="text-primary mb-3">{{ __('app.home_coupon2_title') }}</h3>
                                <p class="mb-3">{{ __('app.home_coupon2_desc') }}</p>
                                <span class="badge bg-primary fs-6">MONTHLY20</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════
     FAQ SECTION
══════════════════════════════════════════════════ -->
@php
$faqs = [
    ['q' => __('app.faq_q1'), 'a' => __('app.faq_a1')],
    ['q' => __('app.faq_q2'), 'a' => __('app.faq_a2')],
    ['q' => __('app.faq_q3'), 'a' => __('app.faq_a3')],
    ['q' => __('app.faq_q4'), 'a' => __('app.faq_a4')],
    ['q' => __('app.faq_q5'), 'a' => __('app.faq_a5')],
    ['q' => __('app.faq_q6'), 'a' => __('app.faq_a6')],
    ['q' => __('app.faq_q7'), 'a' => __('app.faq_a7')],
    ['q' => __('app.faq_q8'), 'a' => __('app.faq_a8')],
];
@endphp

<section id="faq" class="lux-faq-section" aria-labelledby="faq-heading">
    <div class="container">

        {{-- Section header --}}
        <div class="lux-faq-header text-center mb-5">
            <span class="lux-faq-eyebrow">FAQ</span>
            <h2 id="faq-heading" class="lux-faq-title">{{ __('app.faq_section_title') }}</h2>
            <p class="lux-faq-sub">{{ __('app.faq_section_sub') }}</p>
        </div>

        {{-- Accordion --}}
        <div class="row justify-content-center">
            <div class="col-12 col-lg-9 col-xl-8">
                <div class="lux-faq-accordion" id="faqAccordion" role="list">
                    @foreach($faqs as $i => $faq)
                    @php $itemId = 'faq-item-' . $i; $panelId = 'faq-panel-' . $i; @endphp
                    <div class="lux-faq-item{{ $i === 0 ? ' open' : '' }}" role="listitem">
                        <button
                            class="lux-faq-question"
                            id="{{ $itemId }}"
                            aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                            aria-controls="{{ $panelId }}"
                            data-faq-btn>
                            <span class="lux-faq-q-text">{{ $faq['q'] }}</span>
                            <span class="lux-faq-icon" aria-hidden="true">
                                <i class="bi bi-plus-lg lux-faq-plus"></i>
                                <i class="bi bi-dash-lg lux-faq-minus"></i>
                            </span>
                        </button>
                        <div
                            class="lux-faq-answer"
                            id="{{ $panelId }}"
                            role="region"
                            aria-labelledby="{{ $itemId }}"
                            {{ $i !== 0 ? 'hidden' : '' }}>
                            <div class="lux-faq-answer-inner">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- CTA below FAQ --}}
                <div class="lux-faq-cta text-center mt-5">
                    <p class="lux-faq-cta-text">{{ $isRtl ? 'لم تجد إجابتك؟ تواصل معنا مباشرة.' : 'Still have questions? We\'re happy to help.' }}</p>
                    <a href="/contact" class="lux-faq-cta-btn">
                        <i class="bi bi-chat-dots{{ $isRtl ? '' : '-fill' }} {{ $isRtl ? 'ms-2' : 'me-2' }}"></i>
                        {{ $isRtl ? 'تواصل مع فريقنا' : 'Contact Our Team' }}
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- FAQ JSON-LD schema for Google rich results --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $i => $faq)
        {
            "@type": "Question",
            "name": {{ json_encode($faq['q']) }},
            "acceptedAnswer": {
                "@type": "Answer",
                "text": {{ json_encode($faq['a']) }}
            }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>

<style>
    /* ═══════════════════════════════════════════
       FAQ SECTION
    ═══════════════════════════════════════════ */
    .lux-faq-section {
        padding: 5rem 0;
        background: #f8f9fa;
        border-top: 1px solid #efefef;
    }

    /* Header */
    .lux-faq-eyebrow {
        display: inline-block;
        color: #bfa133;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
    }
    .lux-faq-title {
        font-size: clamp(1.6rem, 4vw, 2.4rem);
        font-weight: 800;
        color: #111;
        margin-bottom: 0.6rem;
        line-height: 1.2;
        letter-spacing: {{ $isRtl ? '0' : '-0.01em' }};
    }
    .lux-faq-sub {
        color: #666;
        font-size: {{ $isRtl ? '1rem' : '0.95rem' }};
        max-width: 560px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* Accordion wrapper */
    .lux-faq-accordion {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    /* Item */
    .lux-faq-item {
        background: #fff;
        border: 1.5px solid #ececec;
        border-radius: 0.85rem;
        overflow: hidden;
        transition: border-color 0.22s, box-shadow 0.22s;
    }
    .lux-faq-item.open,
    .lux-faq-item:focus-within {
        border-color: #bfa133;
        box-shadow: 0 4px 18px rgba(191,161,51,0.10);
    }

    /* Question button */
    .lux-faq-question {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem 1.4rem;
        background: transparent;
        border: none;
        cursor: pointer;
        text-align: {{ $isRtl ? 'right' : 'left' }};
        transition: background 0.18s;
    }
    .lux-faq-question:hover { background: #fafafa; }
    .lux-faq-q-text {
        font-size: {{ $isRtl ? '1rem' : '0.95rem' }};
        font-weight: 700;
        color: #111;
        line-height: 1.4;
        flex: 1;
    }

    /* +/- icon */
    .lux-faq-icon {
        width: 28px; height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f0ebe0;
        border-radius: 50%;
        color: #bfa133;
        font-size: 0.85rem;
        flex-shrink: 0;
        transition: background 0.2s, transform 0.25s;
    }
    .lux-faq-item.open .lux-faq-icon {
        background: #bfa133;
        color: #fff;
        transform: rotate(180deg);
    }
    /* Show only the relevant icon */
    .lux-faq-plus  { display: block; }
    .lux-faq-minus { display: none; }
    .lux-faq-item.open .lux-faq-plus  { display: none; }
    .lux-faq-item.open .lux-faq-minus { display: block; }

    /* Answer panel */
    .lux-faq-answer {
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.35s ease, opacity 0.25s ease;
        opacity: 0;
    }
    .lux-faq-item.open .lux-faq-answer {
        max-height: 600px;
        opacity: 1;
    }
    .lux-faq-answer[hidden] { display: block !important; } /* override browser hidden so CSS transition works */
    .lux-faq-answer-inner {
        padding: 0 1.4rem 1.2rem;
        color: #555;
        font-size: {{ $isRtl ? '0.95rem' : '0.9rem' }};
        line-height: 1.75;
        border-top: 1px solid #f0f0f0;
        padding-top: 0.85rem;
    }

    /* CTA */
    .lux-faq-cta-text {
        color: #666;
        font-size: 0.92rem;
        margin-bottom: 0.85rem;
    }
    .lux-faq-cta-btn {
        display: inline-flex;
        align-items: center;
        background: #111;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 0.7rem 1.8rem;
        border-radius: 50px;
        text-decoration: none;
        transition: background 0.22s, transform 0.22s;
    }
    .lux-faq-cta-btn:hover { background: #bfa133; color: #fff; transform: translateY(-2px); }

    /* Responsive */
    @media (max-width: 767px) {
        .lux-faq-section { padding: 3rem 0; }
        .lux-faq-question { padding: 1rem 1.1rem; }
        .lux-faq-q-text { font-size: 0.92rem; }
        .lux-faq-answer-inner { padding: 0 1.1rem 1rem; padding-top: 0.75rem; font-size: 0.88rem; }
        .lux-faq-icon { width: 26px; height: 26px; font-size: 0.78rem; }
    }
    @media (max-width: 575px) {
        .lux-faq-section { padding: 2.5rem 0; }
        .lux-faq-title { font-size: 1.4rem; }
        .lux-faq-question { padding: 0.9rem 1rem; }
        .lux-faq-q-text { font-size: 0.88rem; }
    }
</style>

<script>
(function() {
    document.addEventListener('DOMContentLoaded', function () {
        var buttons = document.querySelectorAll('[data-faq-btn]');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var item    = btn.closest('.lux-faq-item');
                var panel   = document.getElementById(btn.getAttribute('aria-controls'));
                var isOpen  = item.classList.contains('open');

                // Close all
                document.querySelectorAll('.lux-faq-item.open').forEach(function (openItem) {
                    openItem.classList.remove('open');
                    var openBtn   = openItem.querySelector('[data-faq-btn]');
                    var openPanel = document.getElementById(openBtn.getAttribute('aria-controls'));
                    openBtn.setAttribute('aria-expanded', 'false');
                    openPanel.setAttribute('hidden', '');
                });

                // Open clicked (if it was closed)
                if (!isOpen) {
                    item.classList.add('open');
                    btn.setAttribute('aria-expanded', 'true');
                    panel.removeAttribute('hidden');
                }
            });

            // Keyboard: Space / Enter already fire click on <button>
        });
    });
})();
</script>

<style>
    /* ── Car Card ── */
    .car-card-link:hover .luxury-car-card { box-shadow:0 8px 32px 0 rgba(191,161,51,0.18),0 0 0 4px #bfa13333; transition:box-shadow 0.2s; }
    .car-card-link { color:inherit !important; }
    .vehicle-name { font-size:2.2rem !important; font-weight:700 !important; color:#fff !important; }
    .luxury-car-card { background:linear-gradient(180deg,#666 0%,#fff 80%); border-radius:1.2rem; box-shadow:0 8px 32px 0 rgba(0,0,0,0.18); border:3px solid #fff; max-width:100%; min-width:0; overflow:hidden; position:relative; padding-bottom:160px; height:500px; display:flex; flex-direction:column; }
    .luxury-car-card .p-4.pb-0 { padding-top:0.5rem !important; padding-bottom:0.5rem !important; flex:1; display:flex; flex-direction:column; min-height:0; z-index:1; }
    .luxury-card-gradient { background:transparent; border-bottom-left-radius:1.2rem; border-bottom-right-radius:1.2rem; padding:1rem 1.2rem 1.2rem 1.2rem !important; flex-shrink:0; height:160px; display:flex; flex-direction:column; justify-content:space-between; position:absolute; bottom:0; left:0; right:0; }
    .luxury-car-img { max-width:95%; max-height:360px; min-height:300px; object-fit:contain; background:transparent; margin:0.5rem auto; display:block; position:relative; z-index:2; flex-shrink:0; }
    .lux-heading.mb-3 { margin-bottom:0.4rem !important; font-size:0.88rem; }
    .lux-heading { font-size:0.88rem !important; }
    .badge { font-size:0.75rem !important; padding:0.22em 0.7em !important; }
    .luxury-card-gradient .fs-5 { font-size:0.88rem !important; margin-bottom:0 !important; }
    .luxury-card-gradient .lux-heading { font-size:0.85rem !important; margin-bottom:0.2rem !important; }
    .luxury-card-gradient .row { margin-bottom:0.8rem !important; }
    .luxury-card-gradient .d-flex { margin-top:auto; width:100%; }
    .lux-btn-book { background:#aaa; color:#fff; border-radius:0.7rem; font-size:0.85rem; padding:0.32rem 1rem; border:none; transition:background 0.2s; }
    .lux-btn-book:hover { background:#bfa133; color:#fff; }
    .lux-whatsapp-icon img { filter:drop-shadow(0 2px 6px #2222); transition:transform 0.2s; width:36px; height:36px; }
    .lux-whatsapp-icon img:hover { transform:scale(1.1) rotate(-5deg); }
    .luxury-section-title { font-size:80px !important; font-weight:900; letter-spacing:0.18em; text-transform:uppercase; line-height:1.05; color:#111 !important; text-shadow:none; margin-bottom:1.5rem; }
    .hero-section { min-height:600px; height:85vh; max-height:800px; width:100%; position:relative; display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .hero-background { position:absolute; top:0; left:0; width:100%; height:100%; background:linear-gradient(135deg,#ffffff 0%,#f8f9fa 30%,#ffffff 70%,#f5f5f5 100%); z-index:1; }
    .hero-pattern { position:absolute; top:0; left:0; width:100%; height:100%; background-image:radial-gradient(circle at 25% 25%,rgba(191,161,51,0.05) 0%,transparent 50%),radial-gradient(circle at 75% 75%,rgba(191,161,51,0.03) 0%,transparent 50%),linear-gradient(45deg,transparent 30%,rgba(191,161,51,0.02) 50%,transparent 70%); z-index:1; }
    .hero-pattern::after { content:''; position:absolute; top:0; left:0; width:100%; height:100%; background:url('data:image/svg+xml,<svg width="60" height="60" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="dots" width="60" height="60" patternUnits="userSpaceOnUse"><circle cx="30" cy="30" r="1" fill="rgba(191,161,51,0.08)"/></pattern></defs><rect width="60" height="60" fill="url(%23dots)"/></svg>'); opacity:0.3; }
    .hero-section .container { z-index:2; position:relative; }
    .hero-main-title { font-size:4rem; font-weight:900; letter-spacing:0.15em; text-transform:uppercase; color:#1a1a1a; text-shadow:0 1px 3px rgba(0,0,0,0.1); margin-bottom:1.5rem; line-height:1.2; animation:fadeInUp 1s ease-out; }
    .hero-description { font-size:1.15rem; font-weight:300; line-height:1.9; color:#333333; max-width:900px; margin:0 auto 2rem; animation:fadeInUp 1.4s ease-out; }
    .vehicle-filter-wrapper { animation:fadeInUp 1.6s ease-out; background:rgba(255,255,255,0.95); padding:2.5rem; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.1); border:1px solid rgba(191,161,51,0.2); max-width:100%; margin:0 auto; }
    .vehicle-card { transition:opacity 0.3s ease; }
    #reset-search-btn { display:block; margin:20px auto 0; }
    .filter-label { display:block; font-weight:600; color:#1a1a1a; margin-bottom:0.5rem; font-size:0.95rem; text-transform:uppercase; letter-spacing:0.05em; }
    .filter-select { height:50px; border:2px solid rgba(191,161,51,0.3); border-radius:10px; font-size:1rem; padding:0.75rem 1rem; transition:all 0.3s ease; background:#fff; }
    .filter-select:focus { border-color:#bfa133; box-shadow:0 0 0 0.2rem rgba(191,161,51,0.25); outline:none; }
    .filter-select:disabled { background:#f5f5f5; cursor:not-allowed; opacity:0.6; }
    #filter-search-btn:disabled { opacity:0.5; cursor:not-allowed; }
    .btn-hero-primary { background:linear-gradient(135deg,#bfa133 0%,#d4b845 50%,#bfa133 100%); background-size:200% 200%; color:#000; border:none; border-radius:50px; font-weight:700; font-size:1rem; letter-spacing:0.1em; text-transform:uppercase; padding:1rem 2.5rem; transition:all 0.4s ease; box-shadow:0 6px 25px rgba(191,161,51,0.5),0 0 20px rgba(191,161,51,0.3),inset 0 1px 0 rgba(255,255,255,0.2); position:relative; overflow:hidden; }
    .btn-hero-primary::before { content:''; position:absolute; top:0; left:-100%; width:100%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,0.3),transparent); transition:left 0.5s; }
    .btn-hero-primary:hover::before { left:100%; }
    .btn-hero-primary:hover { transform:translateY(-3px); box-shadow:0 8px 35px rgba(191,161,51,0.7),0 0 30px rgba(191,161,51,0.4),inset 0 1px 0 rgba(255,255,255,0.3); background-position:right center; }
    .btn-hero-secondary { background:transparent; color:#bfa133; border:2px solid #bfa133; border-radius:50px; font-weight:600; font-size:1rem; letter-spacing:0.1em; text-transform:uppercase; padding:1rem 2.5rem; transition:all 0.4s ease; box-shadow:0 2px 10px rgba(191,161,51,0.2); }
    .btn-hero-secondary:hover { background:#bfa133; border-color:#bfa133; color:#fff; transform:translateY(-3px); box-shadow:0 6px 25px rgba(191,161,51,0.5),0 0 20px rgba(191,161,51,0.3); }
    @keyframes fadeInUp { from{opacity:0;transform:translateY(40px)} to{opacity:1;transform:translateY(0)} }

    /* RTL fixes for hero section */
    @if($isRtl)
    .hero-main-title { letter-spacing: 0; }
    .luxury-section-title { letter-spacing: 0; }
    .filter-label { letter-spacing: 0; text-transform: none; }
    .btn-hero-primary { letter-spacing: 0; text-transform: none; }
    @endif

    @media (max-width:991px) {
        html, body { overflow-x:hidden; max-width:100%; }
        .hero-main-title { font-size:3rem; }
        .hero-description { font-size:1.05rem; }
        .luxury-section-title { font-size:clamp(2rem,5vw,3.5rem) !important; letter-spacing:0.06em; }
    }
    @media (max-width:767px) {
        html, body { overflow-x:hidden; max-width:100%; }
        * { max-width:100%; box-sizing:border-box; }
        .hero-section { min-height:auto; height:auto; padding:2rem 0 1.5rem; width:100% !important; margin-left:0 !important; margin-right:0 !important; left:auto !important; right:auto !important; }
        .hero-section .row { min-height:auto !important; }
        .hero-section .container, .hero-section .container-fluid { padding-left:1rem !important; padding-right:1rem !important; max-width:100% !important; }
        .hero-main-title { font-size:clamp(1.85rem,9vw,2.5rem); letter-spacing:0.04em; line-height:1.1; margin-bottom:1rem !important; }
        .hero-description { font-size:0.95rem; line-height:1.6; margin-bottom:1.4rem !important; }
        .vehicle-filter-wrapper { padding:1rem; border-radius:18px; }
        .filter-label { font-size:0.78rem; margin-bottom:0.35rem; }
        .filter-select { height:46px; border-radius:12px; font-size:0.95rem; }
        #luxury,#mid-range,#economy,#sports,#vans,#about,#contact,#promotions { margin-top:2rem !important; margin-bottom:2rem !important; padding-left:0.85rem; padding-right:0.85rem; }
        #luxury>.row,#mid-range>.row,#economy>.row,#sports>.row,#vans>.row { --bs-gutter-x:0.5rem; row-gap:1rem; }
        .luxury-section-title { font-size:clamp(1.8rem,10vw,2.65rem) !important; letter-spacing:0.05em; line-height:1.15; margin-bottom:1rem; }
        .vehicle-card { padding-left:0 !important; padding-right:0 !important; margin-bottom:0.75rem !important; }
        .car-card-link { max-width:430px; margin:0 auto; }
        /* ── Mobile car card ── */
        .luxury-car-card {
            height: auto !important;
            min-height: 0 !important;
            padding-bottom: 0 !important;
        }
        /* Bottom section: keep transparent so card gradient shows through */
        .luxury-card-gradient {
            position: static !important;
            height: auto !important;
            width: 100% !important;
            background: transparent !important;
            padding: 10px 12px 14px !important;
        }
        .luxury-car-img { max-height: 200px !important; min-height: 130px !important; }
        .vehicle-name { font-size: 1.1rem !important; }

        /* Pricing: Daily left — Weekly center — Monthly right */
        .luxury-card-gradient .row.text-center {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            margin: 0 0 10px 0 !important;
            padding: 0 !important;
            gap: 0 !important;
            width: 100% !important;
        }
        .luxury-card-gradient .row.text-center .col {
            flex: 0 0 auto !important;
            min-width: 0 !important;
            padding: 0 !important;
        }
        .luxury-card-gradient .row.text-center .col:first-child { text-align: left !important; }
        .luxury-card-gradient .row.text-center .col:nth-child(2) { text-align: center !important; }
        .luxury-card-gradient .row.text-center .col:last-child  { text-align: right !important; }
        .luxury-card-gradient .lux-heading {
            font-size: 0.7rem !important;
            color: #555 !important;
            display: block;
            margin-bottom: 2px !important;
            white-space: nowrap;
        }
        .luxury-card-gradient .fs-5 {
            font-size: 0.82rem !important;
            font-weight: 800 !important;
            color: #111 !important;
            white-space: nowrap;
            display: block;
        }

        /* Actions: Book Now far left, WhatsApp far right */
        .luxury-card-gradient .d-flex {
            width: 100% !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-top: 0 !important;
        }
        .lux-btn-book {
            font-size: 0.78rem !important;
            padding: 0.45rem 1rem !important;
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        .lux-whatsapp-icon { margin-left: auto !important; margin-right: 0 !important; }
        .lux-whatsapp-icon img { width: 36px !important; height: 36px !important; }
        #about .lead { font-size:1rem !important; line-height:1.65 !important; }
        #about p:not(.lead),#contact p,#promotions p { font-size:0.95rem !important; line-height:1.6 !important; }
        #contact { padding-top:1rem !important; }
        #contact .card-body,#promotions .card-body { padding:1.25rem !important; }
        .btn-hero-primary,.btn-hero-secondary { width:100%; max-width:280px; padding:0.9rem 2rem; font-size:0.9rem; }
    }
    @media (max-width:575px) {
        .hero-main-title { font-size:clamp(1.5rem,7.5vw,2rem); }
        .hero-description { font-size:0.88rem; }
        .vehicle-filter-wrapper { padding:0.85rem; }
        .filter-select { height:42px; font-size:0.88rem; }
        #about .col-md-4 i { font-size:2rem !important; }
        #about .col-md-4 h3 { font-size:0.95rem; }
        #contact .card-body,#promotions .card-body { padding:1rem !important; }
        .btn-hero-primary,.btn-hero-secondary { max-width:100%; }
    }
    @media (max-width:380px) {
        .vehicle-name { font-size:0.85rem !important; }
        .lux-btn-book { font-size:0.7rem; padding:0.48rem 0.65rem; }
        .lux-whatsapp-icon img { width:34px; height:34px; }
    }
    /* Booking success modal – mobile */
    @media (max-width:767px) {
        #bookingSuccessModal .modal-header { padding:1.25rem !important; }
        #bookingSuccessModal .modal-header i { font-size:2.5rem !important; }
        #bookingSuccessModal .modal-title { font-size:1.2rem !important; }
        #bookingSuccessModal .modal-body { padding:1rem !important; }
        #bookingSuccessModal .booking-success-details { padding:1rem !important; }
        #bookingSuccessModal .row.g-3 > div { flex:0 0 100%; max-width:100%; }
        #bookingSuccessModal .modal-footer { padding:0.75rem 1rem !important; flex-direction:column; gap:0.5rem; }
        #bookingSuccessModal .modal-footer .btn { width:100%; }
    }
</style>

<!-- Booking Success Modal -->
<div class="modal fade" id="bookingSuccessModal" tabindex="-1" aria-labelledby="bookingSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:20px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#28a745,#20c997); color:white; border:none; padding:2rem;">
                <div class="w-100 text-center">
                    <div class="mb-3"><i class="bi bi-check-circle-fill" style="font-size:4rem; color:white;"></i></div>
                    <h2 class="modal-title mb-0" id="bookingSuccessModalLabel">{{ __('app.modal_booking_success') }}</h2>
                    <p class="mb-0 mt-2" style="opacity:0.9;">{{ __('app.modal_booking_reserved') }}</p>
                </div>
            </div>
            <div class="modal-body" style="padding:2rem;">
                <div class="booking-success-details" style="background:#f8f9fa; border-radius:12px; padding:1.5rem; margin-bottom:1.5rem;">
                    <h3 class="mb-3" style="color:#333;">
                        <i class="bi bi-car-front me-2 text-success"></i>
                        {{ __('app.modal_booking_details') }}
                    </h3>
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted">{{ __('app.modal_booking_vehicle') }}</small>
                            <div class="fw-bold" id="successVehicleName">-</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('app.modal_booking_id') }}</small>
                            <div class="fw-bold" id="successBookingId">-</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('app.modal_booking_uid') }}</small>
                            <div class="fw-bold" id="successExternalUid">-</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('app.modal_booking_start') }}</small>
                            <div class="fw-bold" id="successStartDate">-</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('app.modal_booking_end') }}</small>
                            <div class="fw-bold" id="successEndDate">-</div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <div class="alert alert-info" style="border-radius:12px; border:none; background:linear-gradient(135deg,#e3f2fd,#bbdefb);">
                        <i class="bi bi-telephone-fill me-2"></i>
                        <strong>{{ __('app.modal_booking_next_steps') }}</strong> {{ __('app.modal_booking_next_desc') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border:none; padding:1rem 2rem 2rem;">
                <button type="button" class="btn btn-success px-4 py-2" data-bs-dismiss="modal" style="border-radius:12px;">
                    <i class="bi bi-check me-2"></i>{{ __('app.modal_booking_btn') }}
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('booking_success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const successData = @json(session('booking_success'));
    document.getElementById('successVehicleName').textContent = successData.vehicle_name;
    document.getElementById('successBookingId').textContent = '#' + successData.booking_id;
    document.getElementById('successExternalUid').textContent = successData.external_reservation_uid || '-';
    document.getElementById('successStartDate').textContent = successData.start_date;
    document.getElementById('successEndDate').textContent = successData.end_date;
    const modal = new bootstrap.Modal(document.getElementById('bookingSuccessModal'));
    modal.show();
    setTimeout(function() {
        const confettiContainer = document.createElement('div');
        confettiContainer.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999;';
        document.body.appendChild(confettiContainer);
        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.style.cssText = `position:absolute;width:10px;height:10px;background:${['#ff6b6b','#4ecdc4','#45b7d1','#96ceb4','#feca57'][Math.floor(Math.random()*5)]};top:-10px;left:${Math.random()*100}%;animation:confetti-fall 3s linear forwards;border-radius:50%;`;
            confettiContainer.appendChild(confetti);
        }
        const style = document.createElement('style');
        style.textContent = '@keyframes confetti-fall{to{transform:translateY(100vh) rotate(360deg);opacity:0;}}';
        document.head.appendChild(style);
        setTimeout(()=>{confettiContainer.remove();style.remove();}, 3000);
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
    const makeSelect  = document.getElementById('filter-make');
    const modelSelect = document.getElementById('filter-model');
    const yearSelect  = document.getElementById('filter-year');
    const searchBtn   = document.getElementById('filter-search-btn');
    if (!makeSelect || !modelSelect || !yearSelect || !searchBtn) return;

    const selectModelDefault = @json(__('app.hero_select_model'));
    const selectYearDefault  = @json(__('app.hero_select_year'));

    fetch('/api/vehicles/makes')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.makes) {
                data.makes.forEach(make => {
                    const o = document.createElement('option');
                    o.value = make; o.textContent = make;
                    makeSelect.appendChild(o);
                });
            }
        });

    makeSelect.addEventListener('change', function() {
        const selectedMake = this.value;
        modelSelect.innerHTML = `<option value="">${selectModelDefault}</option>`;
        modelSelect.disabled = !selectedMake;
        yearSelect.innerHTML  = `<option value="">${selectYearDefault}</option>`;
        yearSelect.disabled   = true;
        searchBtn.disabled    = true;
        if (selectedMake) {
            fetch(`/api/vehicles/models?make=${encodeURIComponent(selectedMake)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.models) {
                        data.models.forEach(model => {
                            const o = document.createElement('option');
                            o.value = model; o.textContent = model;
                            modelSelect.appendChild(o);
                        });
                        modelSelect.disabled = false;
                    }
                });
        }
    });

    modelSelect.addEventListener('change', function() {
        const selectedMake  = makeSelect.value;
        const selectedModel = this.value;
        yearSelect.innerHTML = `<option value="">${selectYearDefault}</option>`;
        yearSelect.disabled  = !selectedModel;
        searchBtn.disabled   = true;
        if (selectedMake && selectedModel) {
            fetch(`/api/vehicles/years?make=${encodeURIComponent(selectedMake)}&model=${encodeURIComponent(selectedModel)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.years) {
                        data.years.forEach(year => {
                            const o = document.createElement('option');
                            o.value = year; o.textContent = year;
                            yearSelect.appendChild(o);
                        });
                        yearSelect.disabled = false;
                    }
                });
        }
    });

    yearSelect.addEventListener('change', function() {
        searchBtn.disabled = !this.value;
    });

    searchBtn.addEventListener('click', function() {
        const make  = makeSelect.value.toLowerCase().trim();
        const model = modelSelect.value.toLowerCase().trim();
        const year  = yearSelect.value.trim();

        if (!make || !model || !year) {
            alert(window.trans.searchError);
            return;
        }

        const allVehicleCards = document.querySelectorAll('.vehicle-card');
        const allSections     = document.querySelectorAll('#luxury,#mid-range,#economy,#sports,#vans');
        allVehicleCards.forEach(c => c.style.display = 'none');
        allSections.forEach(s => s.style.display = 'none');

        let foundVehicle = null, foundCategory = null;
        allVehicleCards.forEach(card => {
            const cardMake  = (card.getAttribute('data-make')  || '').toLowerCase().replace(/\s+/g,' ').trim();
            const cardModel = (card.getAttribute('data-model') || '').toLowerCase().replace(/\s+/g,' ').trim();
            const cardYear  = String(card.getAttribute('data-year') || '').trim();
            if (cardMake === make.replace(/\s+/g,' ') && cardModel === model.replace(/\s+/g,' ') && cardYear === year) {
                foundVehicle  = card;
                foundCategory = card.getAttribute('data-category');
            }
        });

        if (foundVehicle && foundCategory) {
            foundVehicle.style.display = 'block';
            const sectionId = foundCategory === 'mid-range' ? 'mid-range' : foundCategory;
            const section   = document.getElementById(sectionId);
            if (section) {
                section.style.display = 'block';
                setTimeout(() => section.scrollIntoView({behavior:'smooth',block:'start'}), 100);
            }
            if (!document.getElementById('reset-search-btn')) {
                const resetBtn = document.createElement('button');
                resetBtn.id = 'reset-search-btn';
                resetBtn.className = 'btn btn-hero-secondary mt-3';
                resetBtn.innerHTML = `<i class="bi bi-arrow-counterclockwise me-2"></i>${window.trans.showAll}`;
                resetBtn.style.cssText = 'display:block;margin:20px auto;';
                resetBtn.onclick = function() {
                    makeSelect.value = ''; modelSelect.value = ''; yearSelect.value = '';
                    modelSelect.disabled = true; yearSelect.disabled = true; searchBtn.disabled = true;
                    allVehicleCards.forEach(c => c.style.display = 'block');
                    allSections.forEach(s => s.style.display = 'block');
                    resetBtn.remove();
                    window.scrollTo({top:0,behavior:'smooth'});
                };
                document.querySelector('.vehicle-filter-wrapper').appendChild(resetBtn);
            }
        } else {
            alert(window.trans.noVehicle);
            allVehicleCards.forEach(c => c.style.display = 'block');
            allSections.forEach(s => s.style.display = 'block');
        }
    });
});
</script>

@endsection
