@extends('layouts.blade_app')

@section('title', 'Contact Us - LUXURIA CARS RENTAL')

@section('content')
<div class="contact-hero-section text-center text-white d-flex align-items-center justify-content-center flex-column">
    <h1 class="display-3 fw-bold mb-3 contact-hero-title">CONTACT US</h1>
    <p class="lead contact-hero-desc">Get in Touch with Luxuria<br>Your luxury experience starts here.</p>
</div>

<div class="container my-5">
    <!-- معلومات التواصل الرئيسية -->
    <div class="row mb-5">
        <div class="col-lg-4 mb-4">
            <div class="contact-card h-100 p-4 text-center shadow rounded">
                <div class="contact-icon mb-3">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <h4 class="text-gold mb-3">Call Us</h4>
                <p class="fs-5 mb-2">+971 50 271 1549</p>
                <p class="fs-5 mb-0">+971 54 270 0030</p>
                <small class="text-muted">Available 24/7</small>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="contact-card h-100 p-4 text-center shadow rounded">
                <div class="contact-icon mb-3">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <h4 class="text-gold mb-3">Email Us</h4>
                <p class="fs-5 mb-2">info@rentalluxuria.com</p>
                <p class="fs-5 mb-0">bookings@rentalluxuria.com</p>
                <small class="text-muted">We reply within 2 hours</small>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="contact-card h-100 p-4 text-center shadow rounded">
                <div class="contact-icon mb-3">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <h4 class="text-gold mb-3">Visit Us</h4>
                <p class="fs-6 mb-0">Shop No 9 - Dr Murad Building<br>Hor Al Anz East - Abu Hail<br>Dubai - UAE</p>
                <small class="text-muted">Open daily 8 AM - 10 PM</small>
            </div>
        </div>
    </div>

    <!-- نموذج التواصل والمعلومات الإضافية -->
    <div class="row align-items-start">
        <div class="col-lg-8 mb-4">
            <div class="contact-form-card p-4 shadow rounded">
                <h3 class="text-gold mb-4">Send Us a Message</h3>
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control contact-input" id="firstName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control contact-input" id="lastName" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control contact-input" id="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control contact-input" id="phone" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="service" class="form-label">Service Interested In</label>
                        <select class="form-select contact-input" id="service">
                            <option>Luxury Car Rental</option>
                            <option>Chauffeur Service</option>
                            <option>Event Transportation</option>
                            <option>Corporate Rentals</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control contact-input" id="message" rows="4" placeholder="Tell us about your requirements..."></textarea>
                    </div>
                    <button type="submit" class="btn contact-btn-submit px-4 py-2">Send Message</button>
                </form>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- ساعات العمل -->
            <div class="contact-hours-card p-4 shadow rounded mb-4">
                <h4 class="text-gold mb-3">Business Hours</h4>
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>Monday - Friday:</strong> 8:00 AM - 10:00 PM</li>
                    <li class="mb-2"><strong>Saturday:</strong> 8:00 AM - 11:00 PM</li>
                    <li class="mb-2"><strong>Sunday:</strong> 9:00 AM - 9:00 PM</li>
                    <li class="text-gold"><strong>Emergency Support:</strong> 24/7</li>
                </ul>
            </div>
            <!-- خدمات إضافية -->
            <div class="contact-services-card p-4 shadow rounded">
                <h4 class="text-gold mb-3">Our Services</h4>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle text-gold me-2"></i> Luxury & Exotic Car Rentals</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-gold me-2"></i> Chauffeur Services</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-gold me-2"></i> Airport Transfers</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-gold me-2"></i> Corporate Events</li>
                    <li><i class="bi bi-check-circle text-gold me-2"></i> Wedding Transportation</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- WhatsApp Contact -->
    <div class="row mt-5">
        <div class="col-12 text-center">
            <div class="whatsapp-contact-section p-4 rounded shadow">
                <h3 class="text-gold mb-3">Need Immediate Assistance?</h3>
                <p class="fs-5 mb-3">Chat with us on WhatsApp for instant bookings and support</p>
                <a href="https://wa.me/971502711549?text=Hi%20Luxuria%20Cars,%20I'm%20interested%20in%20your%20services" target="_blank" class="btn whatsapp-btn px-4 py-2">
                    <i class="bi bi-whatsapp me-2"></i>Chat on WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.contact-hero-section {
    min-height: 300px;
    background: linear-gradient(135deg, #1a1a1a 0%, #bfa133 100%);
    padding: 60px 0 40px 0;
    box-shadow: 0 8px 32px 0 rgba(0,0,0,0.18);
}
.contact-hero-title {
    font-size: 3.5rem;
    letter-spacing: 0.12em;
    color: #fff;
    text-shadow: 0 2px 12px #0008;
}
.contact-hero-desc {
    font-size: 1.3rem;
    color: #f7e7c1;
    text-shadow: 0 2px 8px #0005;
}
.text-gold {
    color: #bfa133 !important;
}
.contact-card {
    background: #fff;
    border: 1px solid #f0f0f0;
    border-top: 4px solid #bfa133;
    transition: transform 0.3s, box-shadow 0.3s;
}
.contact-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(191,161,51,0.15) !important;
}
.contact-icon {
    font-size: 2.5rem;
    color: #bfa133;
}
.contact-form-card {
    background: #fff;
    border: 1px solid #f0f0f0;
    border-left: 6px solid #bfa133;
}
.contact-hours-card, .contact-services-card {
    background: #fff;
    border: 1px solid #f0f0f0;
    border-left: 6px solid #bfa133;
}
.contact-input {
    border: 2px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    transition: border-color 0.3s;
}
.contact-input:focus {
    border-color: #bfa133;
    box-shadow: 0 0 0 0.2rem rgba(191,161,51,0.25);
}
.contact-btn-submit {
    background: #bfa133;
    color: #fff;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: background 0.3s;
}
.contact-btn-submit:hover {
    background: #a88c2c;
    color: #fff;
}
.whatsapp-contact-section {
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    border: 2px solid #bfa133;
}
.whatsapp-btn {
    background: #25d366;
    color: #fff;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    transition: background 0.3s;
}
.whatsapp-btn:hover {
    background: #1da851;
    color: #fff;
}
@media (max-width: 991px) {
    .contact-hero-title {
        font-size: 2.5rem;
    }
    .contact-hero-section {
        padding: 40px 0 24px 0;
    }
}
@media (max-width: 767px) {
    .contact-hero-title {
        font-size: 2rem;
    }
    .contact-hero-section {
        padding: 30px 0 20px 0;
        min-height: 220px;
    }
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
