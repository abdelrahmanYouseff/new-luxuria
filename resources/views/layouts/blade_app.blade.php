<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Luxuria UAE')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/app.css">
    @yield('head')
    <style>
        .lux-navbar {
            background: #111;
            border-bottom: 2px solid #bfa13344;
            font-family: 'Playfair Display', serif;
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
        }
        .lux-navbar .navbar-brand {
            color: #bfa133 !important;
            font-weight: 900;
            font-size: 2.8rem;
            letter-spacing: 2px;
        }
        .lux-navbar .nav-link {
            color: #fff !important;
            font-size: 1.4rem;
            font-weight: 500;
            margin-right: 1rem;
            transition: color 0.2s;
        }
        .lux-navbar .nav-link.active, .lux-navbar .nav-link:hover {
            color: #bfa133 !important;
        }
        .lux-navbar .lux-btn-login {
            background: #bfa133;
            color: #fff !important;
            border-radius: 0.7rem;
            font-weight: 700;
            padding: 0.5rem 1.4rem;
            border: none;
            font-size: 1.4rem;
            margin-left: 1rem;
        }
        body {
            margin-top: 90px; /* Make space for larger navbar */
        }
        @media (max-width: 991px) {
            body { margin-top: 85px; }
            .lux-navbar .navbar-brand {
                font-size: 2.2rem;
            }
            .lux-navbar .nav-link {
                font-size: 1.2rem;
            }
            .lux-navbar .lux-btn-login {
                font-size: 1.2rem;
                padding: 0.4rem 1.2rem;
            }
            .navbar-brand img {
                height: 45px !important;
            }
        }
    </style>
</head>
<body style="background:#fff; color:#111; font-family:'Playfair Display',serif;">
    <nav class="navbar navbar-expand-lg lux-navbar fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="/images_car/new-logo3.png" alt="Luxuria UAE" height="100" class="me-2" style="object-fit: contain;">
            </a>
            <button class="navbar-toggler lux-navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#luxNavbar" aria-controls="luxNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="luxNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/coupons">Coupons</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact us</a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link lux-btn-login" href="/login">Login</a>
                        </li>
                    @else
                        @if(Auth::user()->role === 'user')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle lux-profile-icon" href="#" id="navbarProfileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding:0 1rem;">
                                    <i class="bi bi-person-circle" style="font-size:1.7rem;"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarProfileDropdown">
                                    <li>
                                        <div class="dropdown-item-text">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-star-fill text-warning me-2"></i>
                                                <span class="user-points" data-user-id="{{ Auth::user()->id }}">
                                                    <span class="points-loading">Loading...</span>
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/dashboard">Profile Page</a></li>
                                    <li><a class="dropdown-item" href="/my-points">My Points</a></li>
                                    <li><a class="dropdown-item" href="/view-invoices">Invoices</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="/logout">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        {{-- If admin, show nothing extra --}}
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    @yield('content')
    <footer class="lux-footer-2024 mt-5 pt-5 pb-3">
        <div class="container">
            <!-- Top Contact Row -->
            <div class="row g-4 justify-content-center lux-footer-contact-row mb-4">
                <div class="col-12 col-md-4 text-center">
                    <div class="lux-footer-contact-box h-100 d-flex flex-column align-items-center justify-content-center">
                        <div class="lux-footer-icon mb-2"><i class="bi bi-link-45deg"></i></div>
                        <div class="lux-footer-contact-title">Call us</div>
                        <div class="lux-footer-contact-info">+971502711549</div>
                        <div class="lux-footer-contact-info">+971542700030</div>
                    </div>
                </div>
                <div class="col-12 col-md-4 text-center">
                    <div class="lux-footer-contact-box h-100 d-flex flex-column align-items-center justify-content-center">
                        <div class="lux-footer-icon mb-2"><i class="bi bi-envelope"></i></div>
                        <div class="lux-footer-contact-title">Write to us</div>
                        <div class="lux-footer-contact-info">info@rentalluxuria.com</div>
                    </div>
                </div>
                <div class="col-12 col-md-4 text-center">
                    <div class="lux-footer-contact-box h-100 d-flex flex-column align-items-center justify-content-center">
                        <div class="lux-footer-icon mb-2"><i class="bi bi-geo-alt"></i></div>
                        <div class="lux-footer-contact-title">Address</div>
                        <div class="lux-footer-contact-info">Shop No 9 - Dr Murad Building - Hor Al Anz East - Abu Hail - Dubai - UAE</div>
                    </div>
                </div>
            </div>
            <!-- Main Footer Row -->
            <div class="row gy-5 align-items-start lux-footer-main-row">
                <div class="col-12 col-lg-5 text-center text-lg-start mb-4 mb-lg-0">
                    <div class="lux-footer-logo mb-2 d-flex flex-column align-items-center align-items-lg-start">
                        <img src="/images_car/new-logo3.png" alt="Luxuria UAE" height="100" class="mb-2" style="object-fit: contain;">

                    </div>
                    <div class="lux-footer-slogan mb-3" style="color:#fff;font-size:1.05rem;">The Right Car For Every Road - Rent Your Way</div>
                    <div class="lux-footer-social d-flex gap-3 justify-content-center justify-content-lg-start mb-3">
                        <a href="#" class="lux-footer-social-link" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" class="lux-footer-social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="lux-footer-social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="lux-footer-social-link" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                        <a href="#" class="lux-footer-social-link" aria-label="X"><i class="bi bi-x"></i></a>
                    </div>
                </div>
                <div class="col-12 col-lg-3 text-center text-lg-start mb-4 mb-lg-0">
                    <div class="lux-footer-links-title mb-2">Quick Links</div>
                    <ul class="lux-footer-links list-unstyled mb-0">
                        <li><a href="/" class="lux-footer-link">Home</a></li>
                        <li><a href="#" class="lux-footer-link">About</a></li>
                        <li><a href="#contact" class="lux-footer-link">Contact</a></li>
                        <li><a href="#" class="lux-footer-link">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-12 col-lg-4 text-center text-lg-start">
                    <div class="lux-footer-links-title mb-2">Subscribe</div>
                    <div class="lux-footer-subscribe-text mb-2" style="color:#fff;font-size:1rem;">Want to be notified about our services? Just sign up and we'll send you a notification by email.</div>
                    <form class="lux-footer-subscribe-form d-flex align-items-center justify-content-center justify-content-lg-start">
                        <input type="email" class="form-control lux-footer-input" placeholder="Email Address" style="max-width:220px;">
                        <button type="submit" class="lux-footer-subscribe-btn ms-2"><i class="bi bi-arrow-up-right"></i></button>
                    </form>
                </div>
            </div>
            <div class="lux-footer-bottom text-center mt-5 pt-3" style="color:#fff;font-size:1rem;">
                &copy; {{ date('Y') }} <span style="color:#bfa133;">Luxuria Cars</span>. All rights reserved.
            </div>
        </div>
    </footer>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load user points when dropdown is opened
        const profileDropdown = document.getElementById('navbarProfileDropdown');
        const userPointsElement = document.querySelector('.user-points');

        if (profileDropdown && userPointsElement) {
            profileDropdown.addEventListener('click', function() {
                loadUserPoints();
            });

            // Also load on page load
            loadUserPoints();
        }

        function loadUserPoints() {
            const pointsElement = document.querySelector('.points-loading');
            if (!pointsElement) return;

            pointsElement.innerHTML = 'Loading...';

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
                    pointsElement.innerHTML = `<strong>${data.points}</strong> Points`;
                    pointsElement.style.color = '#bfa133';
                } else {
                    pointsElement.innerHTML = '0 Points';
                    pointsElement.style.color = '#6c757d';
                }
            })
            .catch(error => {
                console.error('Error loading user points:', error);
                pointsElement.innerHTML = '0 Points';
                pointsElement.style.color = '#6c757d';
            });
        }
    });
    </script>
    <style>
        .lux-footer-2024 {
            background: #181818;
            border-top: 2px solid #bfa13344;
            margin-top: 4rem;
            font-family: 'Playfair Display', serif;
        }
        .lux-footer-contact-row {
            border-radius: 1.5rem;
            background: #111;
            box-shadow: 0 2px 24px 0 rgba(191,161,51,0.07);
            padding: 1.5rem 0.5rem 1.2rem 0.5rem;
            margin-bottom: 2.5rem;
        }
        .lux-footer-contact-box {
            background: transparent;
            border-radius: 1.2rem;
            padding: 0.5rem 0.5rem;
        }
        .lux-footer-icon {
            background: #fff;
            color: #bfa133;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 0.5rem auto;
            box-shadow: 0 2px 12px 0 #bfa13322;
        }
        .lux-footer-contact-title {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
        }
        .lux-footer-contact-info {
            color: #fff;
            font-size: 1rem;
            margin-bottom: 0.1rem;
        }
        .lux-footer-logo {
            font-size: 2.2rem;
            font-weight: 900;
            letter-spacing: 2px;
            color: #fff;
        }
        .lux-footer-slogan {
            color: #fff;
            font-size: 1.05rem;
        }
        .lux-footer-social-link {
            color: #fff;
            font-size: 2rem;
            margin: 0 0.2rem;
            transition: color 0.18s, transform 0.18s;
        }
        .lux-footer-social-link:hover {
            color: #bfa133;
            transform: scale(1.1) rotate(-5deg);
        }
        .lux-footer-links-title {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .lux-footer-links {
            padding-left: 0;
        }
        .lux-footer-link {
            color: #fff;
            font-size: 1.05rem;
            text-decoration: none;
            display: block;
            margin-bottom: 0.4rem;
            transition: color 0.18s;
        }
        .lux-footer-link:hover {
            color: #bfa133;
        }
        .lux-footer-subscribe-form {
            max-width: 340px;
        }
        .lux-footer-input {
            border-radius: 2rem;
            border: 1.5px solid #bfa13344;
            background: #222;
            color: #fff;
            font-size: 1rem;
            padding: 0.5rem 1.2rem;
            box-shadow: none;
        }
        .lux-footer-input:focus {
            border-color: #bfa133;
            background: #181818;
            color: #fff;
            box-shadow: 0 0 0 2px #bfa13333;
        }
        .lux-footer-subscribe-btn {
            background: #bfa133;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            transition: background 0.18s;
        }
        .lux-footer-subscribe-btn:hover {
            background: #fff;
            color: #bfa133;
        }
        .lux-footer-bottom {
            border-top: 1px solid #222;
            margin-top: 2.5rem;
            padding-top: 1.2rem;
            color: #fff;
            font-size: 1rem;
        }
        @media (max-width: 991px) {
            .lux-footer-main-row > div { margin-bottom: 2rem; }
            .lux-footer-logo { font-size: 1.5rem !important; }
            .lux-footer-social-link { font-size: 1.5rem; }
        }
        @media (max-width: 767px) {
            .lux-footer-contact-row { padding: 1rem 0.2rem 0.8rem 0.2rem; }
            .lux-footer-main-row { text-align: center !important; }
            .lux-footer-links-title { font-size: 1.1rem; }
            .lux-footer-social-link { font-size: 1.2rem; }
            .lux-footer-logo { font-size: 1.1rem !important; }
        }

        /* User Points Styling */
        .user-points {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .points-loading {
            transition: all 0.3s ease;
        }

        .dropdown-item-text {
            padding: 0.5rem 1rem;
            color: #6c757d;
        }

        .dropdown-item-text:hover {
            background-color: #f8f9fa;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
