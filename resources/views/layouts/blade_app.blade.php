@php $isRtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Primary meta --}}
    <title>@yield('title', __('app.meta_home_title'))</title>
    <meta name="description" content="@yield('meta_description', __('app.meta_home_desc'))" />
    <link rel="canonical" href="@yield('canonical_url', url()->current())" />
    <meta name="robots" content="@yield('robots', 'index, follow')" />

    {{-- Open Graph --}}
    <meta property="og:type"         content="@yield('og_type', 'website')" />
    <meta property="og:title"        content="@yield('title', __('app.meta_home_title'))" />
    <meta property="og:description"  content="@yield('meta_description', __('app.meta_home_desc'))" />
    <meta property="og:url"          content="@yield('canonical_url', url()->current())" />
    <meta property="og:image"        content="@yield('og_image', url('/images_car/new-logo3.png'))" />
    <meta property="og:image:width"  content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:site_name"    content="{{ __('app.site_name') }}" />
    <meta property="og:locale"       content="{{ $isRtl ? 'ar_AE' : 'en_AE' }}" />

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image" />
    <meta name="twitter:title"       content="@yield('title', __('app.meta_home_title'))" />
    <meta name="twitter:description" content="@yield('meta_description', __('app.meta_home_desc'))" />
    <meta name="twitter:image"       content="@yield('og_image', url('/images_car/new-logo3.png'))" />

    {{-- hreflang SEO tags --}}
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="ar" href="{{ url()->current() }}" />
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}" />

    <link rel="icon" type="image/png" href="/images_car/new-logo3.png" />
    <link rel="shortcut icon" type="image/png" href="/images_car/new-logo3.png" />

    {{-- Bootstrap: RTL variant for Arabic, standard for English --}}
    @if($isRtl)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @endif

    {{-- Arabic font (Tajawal — elegant, modern, Gulf-suitable) --}}
    @if($isRtl)
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    @endif

    <link rel="stylesheet" href="/css/app.css">
    @if($isRtl)
        <link rel="stylesheet" href="/css/rtl-overrides.css">
    @endif
    @yield('head')

    <!-- TikTok Pixel Code Start -->
    <script>
    !function (w, d, t) {
      w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(
    var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js",o=n&&n.partner;ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};n=document.createElement("script")
    ;n.type="text/javascript",n.async=!0,n.src=r+"?sdkid="+e+"&lib="+t;e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(n,e)};
      ttq.load('D85BBEBC77U8MI6UKETG');
      ttq.page();
    }(window, document, 'ttq');
    </script>
    <!-- TikTok Pixel Code End -->

    <style>
        /* ── Prevent horizontal scroll on all screens ── */
        html { overflow-x: hidden; max-width: 100%; }

        /* ── Base typography ─────────────────────────────────────── */
        body {
            margin-top: 72px;
            overflow-x: hidden;
            max-width: 100%;
        }
        @if($isRtl)
        body, * {
            font-family: 'Tajawal', sans-serif !important;
        }
        @endif

        /* ── Navbar ──────────────────────────────────────────────── */
        .lux-navbar {
            background: rgba(8, 8, 8, 0.97);
            border-bottom: 1px solid rgba(191,161,51,0.25);
            padding: 0;
            height: 72px;
            transition: background 0.3s, box-shadow 0.3s;
        }
        .lux-navbar.scrolled {
            background: rgba(5, 5, 5, 0.99);
            box-shadow: 0 4px 40px rgba(0,0,0,0.6);
            border-bottom-color: rgba(191,161,51,0.4);
        }
        .lux-navbar > .container-fluid {
            height: 100%;
            display: flex;
            align-items: center;
        }
        /* ── Logo ── */
        .lux-navbar .navbar-brand {
            padding: 0;
            margin-{{ $isRtl ? 'left' : 'right' }}: 0;
            flex-shrink: 0;
        }
        .lux-navbar .navbar-brand img {
            height: 54px;
            width: auto;
            object-fit: contain;
            transition: opacity 0.2s;
        }
        .lux-navbar .navbar-brand:hover img { opacity: 0.85; }

        /* ── Nav links ── */
        .lux-nav-link {
            color: rgba(255,255,255,0.82) !important;
            font-size: {{ $isRtl ? '0.98rem' : '0.78rem' }};
            font-weight: {{ $isRtl ? '500' : '600' }};
            letter-spacing: {{ $isRtl ? '0' : '1.2px' }};
            text-transform: {{ $isRtl ? 'none' : 'uppercase' }};
            padding: 0 {{ $isRtl ? '0.85rem' : '0.9rem' }} !important;
            position: relative;
            text-decoration: none !important;
            transition: color 0.22s;
            white-space: nowrap;
        }
        .lux-nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            {{ $isRtl ? 'right' : 'left' }}: 50%;
            transform: translateX({{ $isRtl ? '50%' : '-50%' }});
            width: 0;
            height: 1.5px;
            background: #bfa133;
            transition: width 0.25s ease;
            border-radius: 2px;
        }
        .lux-nav-link:hover,
        .lux-nav-link.active {
            color: #bfa133 !important;
        }
        .lux-nav-link:hover::after,
        .lux-nav-link.active::after {
            width: 70%;
        }

        /* ── Right-side actions ── */
        .lux-navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-shrink: 0;
        }

        /* Language switcher */
        .lux-lang-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            color: rgba(255,255,255,0.65) !important;
            font-size: {{ $isRtl ? '0.95rem' : '0.72rem' }};
            font-weight: 600;
            letter-spacing: {{ $isRtl ? '0' : '0.8px' }};
            text-transform: {{ $isRtl ? 'none' : 'uppercase' }};
            text-decoration: none !important;
            padding: 0.32rem 0.7rem;
            border: 1px solid rgba(191,161,51,0.3);
            border-radius: 4px;
            transition: color 0.2s, border-color 0.2s, background 0.2s;
        }
        .lux-lang-btn:hover {
            color: #bfa133 !important;
            border-color: #bfa133;
            background: rgba(191,161,51,0.07);
        }
        .lux-lang-btn .lux-lang-globe {
            font-size: 0.9rem;
            opacity: 0.75;
        }

        /* Login button */
        .lux-btn-login {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: #111 !important;
            background: #bfa133;
            font-size: {{ $isRtl ? '0.95rem' : '0.78rem' }};
            font-weight: 700;
            letter-spacing: {{ $isRtl ? '0' : '1px' }};
            text-transform: {{ $isRtl ? 'none' : 'uppercase' }};
            text-decoration: none !important;
            padding: 0.45rem 1.2rem;
            border-radius: 4px;
            border: 1px solid #bfa133;
            transition: background 0.22s, color 0.22s, box-shadow 0.22s;
            white-space: nowrap;
        }
        .lux-btn-login:hover {
            background: transparent;
            color: #bfa133 !important;
            box-shadow: 0 0 18px rgba(191,161,51,0.25);
        }

        /* Profile icon */
        .lux-profile-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #bfa133;
            background: rgba(191,161,51,0.08);
            color: #bfa133 !important;
            font-size: 1.25rem;
            text-decoration: none !important;
            transition: background 0.25s, box-shadow 0.25s, transform 0.2s;
            box-shadow: 0 0 0 0 rgba(191,161,51,0);
        }
        .lux-profile-btn::after { display: none !important; }
        .lux-profile-btn:hover,
        .lux-profile-btn[aria-expanded="true"] {
            background: rgba(191,161,51,0.18);
            box-shadow: 0 0 14px rgba(191,161,51,0.35);
            transform: scale(1.07);
        }

        /* Profile dropdown */
        .lux-dropdown {
            border: 1px solid rgba(191,161,51,0.2);
            border-radius: 10px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.35);
            background: #111;
            min-width: 200px;
            padding: 0.4rem 0;
            margin-top: 8px !important;
        }
        .lux-dropdown .dropdown-item {
            color: rgba(255,255,255,0.8);
            font-size: {{ $isRtl ? '0.95rem' : '0.82rem' }};
            font-weight: 500;
            padding: 0.55rem 1.2rem;
            letter-spacing: {{ $isRtl ? '0' : '0.4px' }};
            transition: color 0.15s, background 0.15s;
        }
        .lux-dropdown .dropdown-item:hover {
            background: rgba(191,161,51,0.1);
            color: #bfa133;
        }
        .lux-dropdown .dropdown-divider { border-color: rgba(191,161,51,0.15); margin: 0.25rem 0; }
        .lux-dropdown-header {
            padding: 0.7rem 1.2rem 0.5rem;
            border-bottom: 1px solid rgba(191,161,51,0.12);
            margin-bottom: 0.2rem;
        }
        .lux-dropdown-points {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: {{ $isRtl ? '0.92rem' : '0.8rem' }};
            font-weight: 600;
            color: #bfa133;
        }
        .lux-dropdown-points i { font-size: 0.95rem; }

        /* ── Mobile toggler ── */
        .lux-toggler {
            width: 36px;
            height: 36px;
            padding: 0;
            border: none !important;
            background: transparent !important;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            outline: none !important;
            box-shadow: none !important;
        }
        .lux-toggler span {
            display: block;
            width: 26px;
            height: 2px;
            background: #fff;
            border-radius: 2px;
            transition: all 0.3s ease;
            transform-origin: center;
        }
        .lux-toggler span:nth-child(1) { width: 20px; }
        .lux-toggler span:nth-child(3) { width: 14px; }
        .lux-toggler[aria-expanded="true"] span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
            width: 26px;
        }
        .lux-toggler[aria-expanded="true"] span:nth-child(2) { opacity: 0; }
        .lux-toggler[aria-expanded="true"] span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
            width: 26px;
        }

        /* ── Gold accent divider between logo and nav ── */
        .lux-nav-divider {
            width: 1px;
            height: 22px;
            background: rgba(191,161,51,0.3);
            margin: 0 1.5rem;
            flex-shrink: 0;
        }

        /* ── RTL-specific overrides ──────────────────────────────── */
        @if($isRtl)
        .dropdown-menu {
            text-align: right;
        }
        .dropdown-item {
            text-align: right;
        }
        .input-group > :not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) {
            border-radius: 0.375rem 0 0 0.375rem !important;
        }
        .form-check {
            padding-right: 1.5em;
            padding-left: 0;
        }
        .form-check-input {
            float: right;
            margin-right: -1.5em;
            margin-left: 0;
        }
        .bi::before {
            display: inline-block;
        }
        /* Keep icons from flipping unintentionally */
        .bi-whatsapp, .bi-telephone, .bi-envelope, .bi-geo-alt,
        .bi-check-circle, .bi-star-fill, .bi-award, .bi-shield-check,
        .bi-headset, .bi-car-front, .bi-check-circle-fill,
        .bi-person-circle, .bi-arrow-up-right {
            transform: scaleX(1) !important;
        }
        /* Pagination RTL */
        .pagination {
            direction: rtl;
        }
        /* Table RTL */
        .table th, .table td {
            text-align: right;
        }
        /* Alert RTL */
        .alert {
            text-align: right;
        }
        /* Modal RTL */
        .modal-header .btn-close {
            margin: -0.5rem auto -0.5rem -0.5rem;
        }
        @endif

        /* ── Responsive ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { margin-top: 72px; }
            .lux-navbar { height: 72px; }
            .lux-navbar .navbar-brand img { height: 46px; }
            .lux-nav-divider { display: none; }

            /* Mobile menu panel */
            .navbar-collapse {
                background: #0a0a0a;
                border-top: 1px solid rgba(191,161,51,0.18);
                margin: 0 -1.5rem;
                padding: 1rem 1.5rem 1.5rem;
                overflow-x: hidden;
            }
            .lux-nav-link {
                font-size: {{ $isRtl ? '1.05rem' : '0.9rem' }} !important;
                padding: 0.75rem 0 !important;
                border-bottom: 1px solid rgba(255,255,255,0.06);
                display: block;
            }
            .lux-nav-link::after { display: none; }
            .lux-navbar-actions {
                margin-top: 1rem;
                gap: 0.6rem;
                flex-wrap: wrap;
            }
            .lux-lang-btn { flex: 1; justify-content: center; min-height: 44px; }
            .lux-btn-login { flex: 2; justify-content: center; padding: 0.65rem 1.2rem; font-size: {{ $isRtl ? '1rem' : '0.88rem' }}; min-height: 44px; }
            .lux-profile-btn { min-width: 44px; min-height: 44px; }
        }
        @media (max-width: 575px) {
            .lux-navbar > .container-fluid { padding-left: 1rem; padding-right: 1rem; }
            .lux-navbar .navbar-brand img { height: 40px; }
            .lux-navbar-actions { gap: 0.5rem; }
        }
    </style>
</head>
@php $bodyFont = $isRtl ? "'Tajawal', sans-serif" : "'Playfair Display', serif"; @endphp
<body style="background:#fff; color:#111; font-family:{!! $bodyFont !!};">

    {{-- Pass JS translations for dynamic messages --}}
    <script>
    window.trans = {
        searchError: @json(__('app.hero_search_error')),
        noVehicle:   @json(__('app.hero_no_vehicle')),
        showAll:     @json(__('app.hero_show_all')),
        isRtl:       {{ $isRtl ? 'true' : 'false' }},
    };
    </script>

    <nav class="navbar navbar-expand-lg lux-navbar fixed-top" id="luxNavbar">
        <div class="container-fluid px-4 px-lg-5">

            {{-- Logo --}}
            <a class="navbar-brand" href="/">
                <img src="/images_car/new-logo3.png" alt="Luxuria UAE">
            </a>

            {{-- Gold divider (desktop only) --}}
            <div class="lux-nav-divider d-none d-lg-block"></div>

            {{-- Mobile toggler --}}
            <button class="lux-toggler {{ $isRtl ? 'ms-auto' : 'ms-auto' }} d-lg-none"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#luxNavbarCollapse"
                    aria-controls="luxNavbarCollapse"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>

            {{-- Collapsible nav --}}
            <div class="collapse navbar-collapse" id="luxNavbarCollapse">
                {{-- Nav links — centered on desktop --}}
                <ul class="navbar-nav {{ $isRtl ? 'me-auto' : 'ms-4 me-auto' }} mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="lux-nav-link nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">{{ __('app.nav_home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="lux-nav-link nav-link {{ request()->is('coupons') ? 'active' : '' }}" href="/coupons">{{ __('app.nav_coupons') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="lux-nav-link nav-link {{ request()->is('about') ? 'active' : '' }}" href="/about">{{ __('app.nav_about') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="lux-nav-link nav-link {{ request()->is('contact') ? 'active' : '' }}" href="/contact">{{ __('app.nav_contact') }}</a>
                    </li>
                </ul>

                {{-- Right-side actions --}}
                <div class="lux-navbar-actions {{ $isRtl ? 'me-lg-0 ms-lg-auto' : '' }}">

                    {{-- Language switcher --}}
                    @if($isRtl)
                        <a class="lux-lang-btn" href="{{ route('lang.switch', 'en') }}" title="Switch to English" aria-label="Switch to English">
                            <span class="lux-lang-globe">🌐</span> EN
                        </a>
                    @else
                        <a class="lux-lang-btn" href="{{ route('lang.switch', 'ar') }}" title="التبديل إلى العربية" aria-label="Switch to Arabic">
                            <span class="lux-lang-globe">🌐</span> عربي
                        </a>
                    @endif

                    {{-- Auth --}}
                    @guest
                        <a class="lux-btn-login" href="/login">
                            <i class="bi bi-person" style="font-size:0.85rem;"></i>
                            {{ __('app.nav_login') }}
                        </a>
                    @else
                        @if(Auth::user()->role === 'user')
                            <div class="dropdown">
                                <a class="lux-profile-btn"
                                   href="#"
                                   id="navbarProfileDropdown"
                                   role="button"
                                   data-bs-toggle="dropdown"
                                   aria-expanded="false"
                                   style="text-decoration:none;">
                                    <i class="bi bi-person-fill"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end lux-dropdown" aria-labelledby="navbarProfileDropdown">
                                    <li class="lux-dropdown-header">
                                        <div class="lux-dropdown-points">
                                            <i class="bi bi-star-fill"></i>
                                            <span class="user-points" data-user-id="{{ Auth::user()->id }}">
                                                <span class="points-loading">{{ __('app.nav_loading') }}</span>
                                            </span>
                                        </div>
                                        <div style="font-size:0.72rem; color:rgba(255,255,255,0.4); margin-top:0.2rem; {{ $isRtl ? '' : 'letter-spacing:0.5px; text-transform:uppercase;' }}">
                                            {{ Auth::user()->name }}
                                        </div>
                                    </li>
                                    <li><a class="dropdown-item" href="/dashboard"><i class="bi bi-person-lines-fill {{ $isRtl ? 'ms-2' : 'me-2' }}" style="color:#bfa133;font-size:0.85rem;"></i>{{ __('app.nav_profile') }}</a></li>
                                    <li><a class="dropdown-item" href="/my-points"><i class="bi bi-award {{ $isRtl ? 'ms-2' : 'me-2' }}" style="color:#bfa133;font-size:0.85rem;"></i>{{ __('app.nav_my_points') }}</a></li>
                                    <li><a class="dropdown-item" href="/view-invoices"><i class="bi bi-receipt {{ $isRtl ? 'ms-2' : 'me-2' }}" style="color:#bfa133;font-size:0.85rem;"></i>{{ __('app.nav_invoices') }}</a></li>
                                    <li><hr class="dropdown-divider lux-dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="/logout">
                                            @csrf
                                            <button type="submit" class="dropdown-item" style="background:none;border:none;width:100%;text-align:{{ $isRtl ? 'right' : 'left' }};cursor:pointer;">
                                                <i class="bi bi-box-arrow-{{ $isRtl ? 'right' : 'left' }} {{ $isRtl ? 'ms-2' : 'me-2' }}" style="color:#ef4444;font-size:0.85rem;"></i>{{ __('app.nav_logout') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- Scroll effect for navbar --}}
    <script>
    (function(){
        var nav = document.getElementById('luxNavbar');
        if(!nav) return;
        function onScroll(){ nav.classList.toggle('scrolled', window.scrollY > 30); }
        window.addEventListener('scroll', onScroll, {passive:true});
        onScroll();
    })();
    </script>

    @yield('content')

    <footer class="lux-footer">
        {{-- Gold top accent bar --}}
        <div class="lux-footer-accent"></div>

        <div class="container lux-footer-inner">
            <div class="row gy-5 lux-footer-grid">

                {{-- Col 1 · Brand --}}
                <div class="col-12 col-lg-4">
                    <div class="lux-footer-brand">
                        <img src="/images_car/new-logo3.png" alt="Luxuria UAE" height="80" class="lux-footer-logo-img mb-3">
                        <p class="lux-footer-slogan">{{ __('app.footer_slogan') }}</p>
                        <p class="lux-footer-brand-desc">{{ __('app.footer_brand_desc') }}</p>
                        <div class="lux-footer-socials">
                            <a href="https://wa.me/971502711549" class="lux-fsoc" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                            <a href="#" class="lux-fsoc" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="lux-fsoc" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="lux-fsoc" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                            <a href="#" class="lux-fsoc" aria-label="X / Twitter"><i class="bi bi-twitter-x"></i></a>
                        </div>
                    </div>
                </div>

                {{-- Col 2 · Quick Links --}}
                <div class="col-6 col-lg-2">
                    <h3 class="lux-footer-col-title">{{ __('app.footer_quick_links') }}</h3>
                    <ul class="lux-footer-list">
                        <li><a href="/" class="lux-flink"><i class="bi bi-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('app.nav_home') }}</a></li>
                        <li><a href="/#luxury" class="lux-flink"><i class="bi bi-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('app.footer_fleet_link') }}</a></li>
                        <li><a href="/coupons" class="lux-flink"><i class="bi bi-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('app.nav_coupons') }}</a></li>
                        <li><a href="/about" class="lux-flink"><i class="bi bi-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('app.nav_about') }}</a></li>
                        <li><a href="/contact" class="lux-flink"><i class="bi bi-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('app.nav_contact') }}</a></li>
                        <li><a href="/privacy-policy" class="lux-flink"><i class="bi bi-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>{{ __('app.footer_privacy') }}</a></li>
                    </ul>
                </div>

                {{-- Col 3 · Contact --}}
                <div class="col-6 col-lg-3">
                    <h3 class="lux-footer-col-title">{{ __('app.footer_contact_title') }}</h3>
                    <ul class="lux-footer-list lux-footer-contact-list">
                        <li>
                            <i class="bi bi-telephone-fill lux-fc-icon"></i>
                            <span>
                                <a href="tel:+971502711549" class="lux-flink">+971 50 271 1549</a><br>
                                <a href="tel:+971542700030" class="lux-flink">+971 54 270 0030</a>
                            </span>
                        </li>
                        <li>
                            <i class="bi bi-envelope-fill lux-fc-icon"></i>
                            <a href="mailto:info@rentalluxuria.com" class="lux-flink">info@rentalluxuria.com</a>
                        </li>
                        <li>
                            <i class="bi bi-geo-alt-fill lux-fc-icon"></i>
                            <span>{{ __('app.footer_address_val') }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Col 4 · Hours + Newsletter --}}
                <div class="col-12 col-lg-3">
                    <h3 class="lux-footer-col-title">{{ __('app.footer_hours_title') }}</h3>
                    <ul class="lux-footer-list lux-footer-hours-list mb-4">
                        <li><i class="bi bi-clock lux-fc-icon"></i><span>{{ __('app.footer_hours_mf') }}</span></li>
                        <li><i class="bi bi-clock lux-fc-icon"></i><span>{{ __('app.footer_hours_sat') }}</span></li>
                        <li><i class="bi bi-clock lux-fc-icon"></i><span>{{ __('app.footer_hours_sun') }}</span></li>
                        <li class="lux-footer-emergency"><i class="bi bi-headset lux-fc-icon"></i><span>{{ __('app.footer_hours_emergency') }}</span></li>
                    </ul>

                    <h3 class="lux-footer-col-title">{{ __('app.footer_subscribe') }}</h3>
                    <p class="lux-footer-brand-desc mb-2">{{ __('app.footer_subscribe_text') }}</p>
                    <form class="lux-footer-newsletter" onsubmit="return false;">
                        <input type="email" class="lux-fn-input" placeholder="{{ __('app.footer_email_ph') }}" aria-label="Email">
                        <button type="submit" class="lux-fn-btn" aria-label="{{ __('app.footer_subscribe_btn') }}">
                            <i class="bi bi-send{{ $isRtl ? '-fill' : '' }}"></i>
                        </button>
                    </form>
                </div>

            </div>{{-- /row --}}
        </div>{{-- /container --}}

        {{-- Bottom bar --}}
        <div class="lux-footer-bottom">
            <div class="container">
                <div class="lux-footer-bottom-inner">
                    <span>&copy; {{ date('Y') }} <strong>Luxuria Cars UAE</strong>. {{ __('app.footer_rights') }}</span>
                    <a href="/privacy-policy" class="lux-footer-bottom-link">{{ __('app.footer_privacy') }}</a>
                    <a href="#top" class="lux-footer-top-btn" aria-label="Back to top"><i class="bi bi-arrow-up"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileDropdown = document.getElementById('navbarProfileDropdown');
        const userPointsElement = document.querySelector('.user-points');
        const pointsUnit = @json(__('app.nav_points_unit'));

        if (profileDropdown && userPointsElement) {
            profileDropdown.addEventListener('click', function() {
                loadUserPoints();
            });
            loadUserPoints();
        }

        function loadUserPoints() {
            const pointsElement = document.querySelector('.points-loading');
            if (!pointsElement) return;

            pointsElement.innerHTML = @json(__('app.nav_loading'));

            fetch('/user-points', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.points !== undefined) {
                    pointsElement.innerHTML = `<strong>${data.points}</strong> ${pointsUnit}`;
                    pointsElement.style.color = '#bfa133';
                } else {
                    pointsElement.innerHTML = `0 ${pointsUnit}`;
                    pointsElement.style.color = '#6c757d';
                }
            })
            .catch(error => {
                console.error('Error loading user points:', error);
                pointsElement.innerHTML = `0 ${pointsUnit}`;
                pointsElement.style.color = '#6c757d';
            });
        }
    });
    </script>

    <style>
        /* ═══════════════════════════════════════════════
           LUXURIA FOOTER  – Professional 2025 Edition
        ═══════════════════════════════════════════════ */
        .lux-footer {
            background: #0d0d0d;
            color: #b0b0b0;
            font-family: {!! $isRtl ? "'Tajawal', sans-serif" : "inherit" !!};
            margin-top: 0;
            position: relative;
        }

        /* ── Gold accent top strip ── */
        .lux-footer-accent {
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, #bfa133 30%, #e6c84b 50%, #bfa133 70%, transparent 100%);
        }

        /* ── Inner padding ── */
        .lux-footer-inner { padding: 4rem 0 3rem; }

        /* ── Column headings ── */
        .lux-footer-col-title {
            color: #bfa133;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            margin-bottom: 1.4rem;
            position: relative;
            padding-bottom: 0.65rem;
        }
        .lux-footer-col-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            {{ $isRtl ? 'right' : 'left' }}: 0;
            width: 28px;
            height: 2px;
            background: #bfa133;
            border-radius: 2px;
        }

        /* ── Brand column ── */
        .lux-footer-brand { max-width: 340px; {{ $isRtl ? 'margin-right: 0; margin-left: auto;' : '' }} }
        .lux-footer-logo-img { object-fit: contain; filter: brightness(1); display: block; }
        .lux-footer-slogan {
            color: #fff;
            font-size: {{ $isRtl ? '1.05rem' : '1rem' }};
            font-weight: 600;
            margin-bottom: 0.6rem;
            letter-spacing: {{ $isRtl ? '0' : '0.04em' }};
        }
        .lux-footer-brand-desc {
            color: #888;
            font-size: {{ $isRtl ? '0.92rem' : '0.85rem' }};
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        /* ── Social icons ── */
        .lux-footer-socials { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .lux-fsoc {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px; height: 38px;
            border: 1.5px solid #2e2e2e;
            border-radius: 50%;
            color: #888;
            font-size: 1rem;
            text-decoration: none;
            transition: border-color 0.22s, color 0.22s, background 0.22s, transform 0.22s;
        }
        .lux-fsoc:hover {
            border-color: #bfa133;
            color: #bfa133;
            background: #bfa13310;
            transform: translateY(-2px);
        }

        /* ── Link lists ── */
        .lux-footer-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .lux-footer-list li {
            margin-bottom: 0.6rem;
            display: flex;
            align-items: flex-start;
            gap: 0.45rem;
        }
        .lux-flink {
            color: #888;
            text-decoration: none;
            font-size: {{ $isRtl ? '0.92rem' : '0.86rem' }};
            transition: color 0.2s;
            line-height: 1.5;
        }
        .lux-flink:hover { color: #bfa133; }
        .lux-footer-list .bi-chevron-right,
        .lux-footer-list .bi-chevron-left {
            color: #bfa133;
            font-size: 0.65rem;
            margin-top: 0.3rem;
            flex-shrink: 0;
        }

        /* ── Contact list ── */
        .lux-footer-contact-list li { align-items: flex-start; gap: 0.6rem; margin-bottom: 0.9rem; }
        .lux-fc-icon { color: #bfa133; font-size: 0.85rem; margin-top: 0.18rem; flex-shrink: 0; }
        .lux-footer-contact-list span,
        .lux-footer-contact-list a { font-size: {{ $isRtl ? '0.9rem' : '0.83rem' }}; color: #888; line-height: 1.55; }

        /* ── Hours list ── */
        .lux-footer-hours-list li { margin-bottom: 0.55rem; }
        .lux-footer-hours-list span { font-size: {{ $isRtl ? '0.9rem' : '0.82rem' }}; color: #888; }
        .lux-footer-emergency { color: #bfa133 !important; }
        .lux-footer-emergency span { color: #bfa133 !important; font-weight: 600; }

        /* ── Newsletter ── */
        .lux-footer-newsletter {
            display: flex;
            border: 1.5px solid #2a2a2a;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: border-color 0.2s;
        }
        .lux-footer-newsletter:focus-within { border-color: #bfa133; }
        .lux-fn-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: #fff;
            font-size: {{ $isRtl ? '0.9rem' : '0.82rem' }};
            padding: 0.6rem 0.9rem;
        }
        .lux-fn-input::placeholder { color: #555; }
        .lux-fn-btn {
            background: #bfa133;
            border: none;
            color: #fff;
            padding: 0 1rem;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .lux-fn-btn:hover { background: #a88c2c; }

        /* ── Divider ── */
        .lux-footer-bottom {
            border-top: 1px solid #1e1e1e;
            padding: 1.1rem 0;
            background: #0a0a0a;
        }
        .lux-footer-bottom-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            font-size: {{ $isRtl ? '0.88rem' : '0.8rem' }};
            color: #555;
        }
        .lux-footer-bottom-inner strong { color: #bfa133; font-weight: 600; }
        .lux-footer-bottom-link {
            color: #555;
            text-decoration: none;
            transition: color 0.2s;
        }
        .lux-footer-bottom-link:hover { color: #bfa133; }
        .lux-footer-top-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px; height: 32px;
            border: 1.5px solid #2e2e2e;
            border-radius: 50%;
            color: #888;
            font-size: 0.9rem;
            text-decoration: none;
            transition: border-color 0.2s, color 0.2s, background 0.2s;
        }
        .lux-footer-top-btn:hover { border-color: #bfa133; color: #bfa133; background: #bfa13310; }

        /* ── Misc ── */
        .user-points { font-size: 0.88rem; font-weight: 700; }
        .points-loading { transition: all 0.3s ease; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            .lux-footer-inner { padding: 3rem 0 2rem; }
            .lux-footer-brand { max-width: 100%; }
            .lux-footer-socials { justify-content: {{ $isRtl ? 'flex-end' : 'flex-start' }}; }
        }
        @media (max-width: 767px) {
            .lux-footer-col-title { font-size: 0.7rem; }
            .lux-footer-inner { padding: 2.5rem 0 1.5rem; }
            .lux-footer-bottom-inner { justify-content: center; text-align: center; }
            .lux-footer-top-btn { display: none; }
            /* Stack Quick Links + Contact side-by-side cols to full-width */
            .lux-footer-grid .col-6 { flex: 0 0 100%; max-width: 100%; }
            .lux-flink { font-size: 0.9rem; }
            .lux-footer-contact-list span,
            .lux-footer-contact-list a { font-size: 0.88rem; word-break: break-word; }
            .lux-fn-btn { min-width: 44px; min-height: 44px; }
            .lux-fn-input { font-size: 0.9rem; }
            .lux-footer-brand-desc { font-size: 0.88rem; }
        }
        @media (max-width: 575px) {
            .lux-footer-inner { padding: 2rem 0 1rem; }
            .lux-footer-logo-img { height: 60px !important; }
            .lux-footer-slogan { font-size: 0.95rem; }
            .lux-fsoc { width: 34px; height: 34px; font-size: 0.9rem; }
            .lux-footer-bottom-inner { font-size: 0.78rem; gap: 0.4rem; }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
