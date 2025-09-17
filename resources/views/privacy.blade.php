@extends('layouts.blade_app')

@section('title', 'Privacy Policy - LUXURIA CARS RENTAL')

@section('content')
<div class="privacy-hero-section text-center text-white d-flex align-items-center justify-content-center flex-column">
    <h1 class="display-3 fw-bold mb-3 privacy-hero-title">PRIVACY POLICY</h1>
    <p class="lead privacy-hero-desc">Your Privacy Matters to Us<br>Protecting your information with transparency and care.</p>
</div>

<div class="container my-5">
    <!-- Last Updated Notice -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="privacy-notice-card p-3 rounded shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-check text-gold me-2 fs-4"></i>
                    <div>
                        <strong class="text-gold">Last Updated:</strong> {{ date('F d, Y') }}
                        <br><small class="text-muted">This privacy policy is effective immediately and applies to all users of our services.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table of Contents -->
    <div class="row mb-5">
        <div class="col-lg-3">
            <div class="privacy-toc-card p-3 rounded shadow-sm position-sticky" style="top: 120px;">
                <h5 class="text-gold mb-3">Table of Contents</h5>
                <ul class="list-unstyled privacy-toc-list">
                    <li><a href="#section-1" class="privacy-toc-link">1. Information We Collect</a></li>
                    <li><a href="#section-2" class="privacy-toc-link">2. How We Use Information</a></li>
                    <li><a href="#section-3" class="privacy-toc-link">3. Information Sharing</a></li>
                    <li><a href="#section-4" class="privacy-toc-link">4. Data Security</a></li>
                    <li><a href="#section-5" class="privacy-toc-link">5. Cookies & Tracking</a></li>
                    <li><a href="#section-6" class="privacy-toc-link">6. Your Rights</a></li>
                    <li><a href="#section-7" class="privacy-toc-link">7. Third-Party Services</a></li>
                    <li><a href="#section-8" class="privacy-toc-link">8. Data Retention</a></li>
                    <li><a href="#section-9" class="privacy-toc-link">9. International Transfers</a></li>
                    <li><a href="#section-10" class="privacy-toc-link">10. Children's Privacy</a></li>
                    <li><a href="#section-11" class="privacy-toc-link">11. Contact Us</a></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-9">
            <!-- Introduction -->
            <div class="privacy-section mb-5">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h2 class="text-gold mb-3">Welcome to Luxuria Cars Rental</h2>
                    <p class="fs-6 mb-3">
                        At Luxuria Cars Rental ("we," "our," or "us"), we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website <strong>https://rentluxuria.com</strong>, use our mobile application, or engage with our luxury car rental services in the United Arab Emirates.
                    </p>
                    <div class="privacy-highlight-box p-3 rounded mb-3">
                        <h6 class="text-gold mb-2">Our Commitment</h6>
                        <p class="mb-0">We respect your privacy and are committed to protecting your personal data. This policy will help you understand what information we collect, how we use it, and what choices you have.</p>
                    </div>
                </div>
            </div>

            <!-- Section 1: Information We Collect -->
            <div class="privacy-section mb-5" id="section-1">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">1. Information We Collect</h3>
                    
                    <h5 class="mb-3">1.1 Personal Information</h5>
                    <p>We collect personal information that you voluntarily provide to us when you:</p>
                    <ul class="privacy-list">
                        <li><strong>Register an account:</strong> Name, email address, phone number, date of birth</li>
                        <li><strong>Make a reservation:</strong> Driving license details, payment information, pickup/drop-off preferences</li>
                        <li><strong>Contact us:</strong> Name, email, phone number, inquiry details</li>
                        <li><strong>Subscribe to newsletters:</strong> Email address and preferences</li>
                    </ul>

                    <h5 class="mb-3 mt-4">1.2 Automatically Collected Information</h5>
                    <ul class="privacy-list">
                        <li><strong>Device Information:</strong> IP address, browser type, operating system, device identifiers</li>
                        <li><strong>Usage Data:</strong> Pages visited, time spent, click patterns, referral sources</li>
                        <li><strong>Location Data:</strong> GPS coordinates (with your permission) for vehicle pickup/delivery</li>
                        <li><strong>Cookies and Tracking:</strong> Session data, preferences, authentication tokens</li>
                    </ul>

                    <h5 class="mb-3 mt-4">1.3 Payment Information</h5>
                    <p>Payment processing is handled by secure third-party providers (Stripe). We do not store complete credit card numbers or sensitive payment data on our servers.</p>
                </div>
            </div>

            <!-- Section 2: How We Use Information -->
            <div class="privacy-section mb-5" id="section-2">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">2. How We Use Your Information</h3>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="privacy-use-box p-3 rounded h-100">
                                <h6 class="text-gold mb-2">Service Delivery</h6>
                                <ul class="privacy-list-small">
                                    <li>Process car rental bookings</li>
                                    <li>Manage your account</li>
                                    <li>Provide customer support</li>
                                    <li>Send booking confirmations</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="privacy-use-box p-3 rounded h-100">
                                <h6 class="text-gold mb-2">Communication</h6>
                                <ul class="privacy-list-small">
                                    <li>Send important service updates</li>
                                    <li>Respond to inquiries</li>
                                    <li>Marketing communications (with consent)</li>
                                    <li>Customer satisfaction surveys</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="privacy-use-box p-3 rounded h-100">
                                <h6 class="text-gold mb-2">Security & Legal</h6>
                                <ul class="privacy-list-small">
                                    <li>Prevent fraud and abuse</li>
                                    <li>Comply with legal obligations</li>
                                    <li>Protect our rights and property</li>
                                    <li>Ensure platform security</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="privacy-use-box p-3 rounded h-100">
                                <h6 class="text-gold mb-2">Improvement</h6>
                                <ul class="privacy-list-small">
                                    <li>Analyze usage patterns</li>
                                    <li>Improve our services</li>
                                    <li>Develop new features</li>
                                    <li>Personalize user experience</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Information Sharing -->
            <div class="privacy-section mb-5" id="section-3">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">3. How We Share Your Information</h3>
                    
                    <div class="privacy-important-notice mb-4">
                        <i class="bi bi-shield-check text-gold me-2"></i>
                        <strong>We do not sell, rent, or trade your personal information to third parties for marketing purposes.</strong>
                    </div>

                    <h5 class="mb-3">We may share your information in the following circumstances:</h5>
                    <ul class="privacy-list">
                        <li><strong>Service Providers:</strong> Payment processors (Stripe), SMS providers, email services, analytics tools</li>
                        <li><strong>Legal Requirements:</strong> When required by law, court order, or regulatory authority</li>
                        <li><strong>Business Protection:</strong> To protect our rights, property, or safety, or that of our users</li>
                        <li><strong>Business Transfers:</strong> In connection with merger, acquisition, or sale of assets</li>
                        <li><strong>With Your Consent:</strong> When you explicitly agree to share information</li>
                    </ul>
                </div>
            </div>

            <!-- Section 4: Data Security -->
            <div class="privacy-section mb-5" id="section-4">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">4. Data Security</h3>
                    
                    <p class="mb-4">We implement comprehensive security measures to protect your personal information:</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="privacy-security-item d-flex align-items-start">
                                <i class="bi bi-shield-lock text-gold me-3 fs-4"></i>
                                <div>
                                    <h6>Encryption</h6>
                                    <p class="mb-0">All data transmission uses SSL/TLS encryption</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="privacy-security-item d-flex align-items-start">
                                <i class="bi bi-server text-gold me-3 fs-4"></i>
                                <div>
                                    <h6>Secure Storage</h6>
                                    <p class="mb-0">Data stored on secure, encrypted servers</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="privacy-security-item d-flex align-items-start">
                                <i class="bi bi-person-check text-gold me-3 fs-4"></i>
                                <div>
                                    <h6>Access Control</h6>
                                    <p class="mb-0">Limited access on need-to-know basis</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="privacy-security-item d-flex align-items-start">
                                <i class="bi bi-arrow-clockwise text-gold me-3 fs-4"></i>
                                <div>
                                    <h6>Regular Updates</h6>
                                    <p class="mb-0">Continuous security monitoring and updates</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Cookies & Tracking -->
            <div class="privacy-section mb-5" id="section-5">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">5. Cookies and Tracking Technologies</h3>
                    
                    <p class="mb-4">We use cookies and similar technologies to enhance your experience:</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered privacy-cookies-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cookie Type</th>
                                    <th>Purpose</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Essential</strong></td>
                                    <td>Authentication, security, basic functionality</td>
                                    <td>Session/1 year</td>
                                </tr>
                                <tr>
                                    <td><strong>Analytics</strong></td>
                                    <td>Usage statistics, performance monitoring</td>
                                    <td>2 years</td>
                                </tr>
                                <tr>
                                    <td><strong>Functional</strong></td>
                                    <td>Remember preferences, language settings</td>
                                    <td>1 year</td>
                                </tr>
                                <tr>
                                    <td><strong>Marketing</strong></td>
                                    <td>Personalized content, advertising (with consent)</td>
                                    <td>1 year</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <p class="mt-3">You can manage cookie preferences through your browser settings. Note that disabling essential cookies may affect website functionality.</p>
                </div>
            </div>

            <!-- Section 6: Your Rights -->
            <div class="privacy-section mb-5" id="section-6">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">6. Your Privacy Rights</h3>
                    
                    <p class="mb-4">Under applicable privacy laws, you have the following rights:</p>
                    
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <div class="privacy-right-card p-3 rounded h-100">
                                <h6 class="text-gold mb-2"><i class="bi bi-eye me-2"></i>Right to Access</h6>
                                <p class="mb-0">Request a copy of the personal data we hold about you</p>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="privacy-right-card p-3 rounded h-100">
                                <h6 class="text-gold mb-2"><i class="bi bi-pencil me-2"></i>Right to Rectification</h6>
                                <p class="mb-0">Request correction of inaccurate or incomplete data</p>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="privacy-right-card p-3 rounded h-100">
                                <h6 class="text-gold mb-2"><i class="bi bi-trash me-2"></i>Right to Erasure</h6>
                                <p class="mb-0">Request deletion of your personal data (subject to legal obligations)</p>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="privacy-right-card p-3 rounded h-100">
                                <h6 class="text-gold mb-2"><i class="bi bi-pause me-2"></i>Right to Restrict Processing</h6>
                                <p class="mb-0">Request limitation of how we process your data</p>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="privacy-right-card p-3 rounded h-100">
                                <h6 class="text-gold mb-2"><i class="bi bi-download me-2"></i>Right to Data Portability</h6>
                                <p class="mb-0">Receive your data in a structured, machine-readable format</p>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="privacy-right-card p-3 rounded h-100">
                                <h6 class="text-gold mb-2"><i class="bi bi-x-circle me-2"></i>Right to Object</h6>
                                <p class="mb-0">Object to processing for marketing or legitimate interests</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="privacy-contact-rights mt-4 p-3 rounded">
                        <h6 class="text-gold mb-2">How to Exercise Your Rights</h6>
                        <p class="mb-1">To exercise any of these rights, please contact us at:</p>
                        <p class="mb-0"><strong>Email:</strong> privacy@rentluxuria.com | <strong>Phone:</strong> +971 50 271 1549</p>
                    </div>
                </div>
            </div>

            <!-- Section 7: Third-Party Services -->
            <div class="privacy-section mb-5" id="section-7">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">7. Third-Party Services</h3>
                    
                    <p class="mb-4">We work with trusted third-party service providers who help us deliver our services:</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="privacy-third-party-card p-3 rounded">
                                <h6 class="text-gold mb-2">Payment Processing</h6>
                                <p class="mb-1"><strong>Stripe:</strong> Secure payment processing</p>
                                <small class="text-muted">Subject to Stripe's privacy policy</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="privacy-third-party-card p-3 rounded">
                                <h6 class="text-gold mb-2">Analytics</h6>
                                <p class="mb-1"><strong>Google Analytics:</strong> Website usage analytics</p>
                                <small class="text-muted">Anonymized data collection</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="privacy-third-party-card p-3 rounded">
                                <h6 class="text-gold mb-2">Communication</h6>
                                <p class="mb-1"><strong>Email/SMS Services:</strong> Notifications and updates</p>
                                <small class="text-muted">Secure, encrypted transmission</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="privacy-third-party-card p-3 rounded">
                                <h6 class="text-gold mb-2">Maps & Location</h6>
                                <p class="mb-1"><strong>Google Maps:</strong> Location services</p>
                                <small class="text-muted">For pickup/delivery coordination</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 8: Data Retention -->
            <div class="privacy-section mb-5" id="section-8">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">8. Data Retention</h3>
                    
                    <p class="mb-4">We retain your personal information for as long as necessary to provide our services and comply with legal obligations:</p>
                    
                    <ul class="privacy-list">
                        <li><strong>Account Information:</strong> Retained while your account is active and for 3 years after closure</li>
                        <li><strong>Booking Records:</strong> Kept for 7 years for legal and tax compliance</li>
                        <li><strong>Payment Data:</strong> Processed by third parties, not stored by us</li>
                        <li><strong>Marketing Data:</strong> Until you unsubscribe or withdraw consent</li>
                        <li><strong>Legal Claims:</strong> Retained as long as legally required</li>
                    </ul>
                    
                    <div class="privacy-retention-notice mt-4 p-3 rounded">
                        <i class="bi bi-info-circle text-gold me-2"></i>
                        <strong>Automatic Deletion:</strong> We automatically delete data that is no longer needed, unless legal obligations require longer retention.
                    </div>
                </div>
            </div>

            <!-- Section 9: International Transfers -->
            <div class="privacy-section mb-5" id="section-9">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">9. International Data Transfers</h3>
                    
                    <p class="mb-4">Your information may be transferred to and processed in countries other than the UAE:</p>
                    
                    <div class="privacy-transfer-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-gold mb-2">Data Processing Locations</h6>
                                <ul class="privacy-list">
                                    <li><strong>Primary:</strong> United Arab Emirates (UAE)</li>
                                    <li><strong>Cloud Services:</strong> European Union, United States (with adequate protections)</li>
                                    <li><strong>Payment Processing:</strong> Stripe's secure global infrastructure</li>
                                </ul>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="bi bi-globe text-gold" style="font-size: 4rem;"></i>
                            </div>
                        </div>
                        <div class="privacy-transfer-safeguards mt-3 p-3 rounded">
                            <h6 class="text-gold mb-2">Transfer Safeguards</h6>
                            <p class="mb-0">All international transfers are protected by appropriate safeguards, including standard contractual clauses and adequacy decisions.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 10: Children's Privacy -->
            <div class="privacy-section mb-5" id="section-10">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">10. Children's Privacy</h3>
                    
                    <div class="privacy-children-notice p-4 rounded text-center">
                        <i class="bi bi-shield-check text-gold mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-gold mb-3">Age Restriction</h5>
                        <p class="fs-5 mb-3">Our services are intended for individuals aged 18 and above. We do not knowingly collect personal information from children under 18.</p>
                        <p class="mb-0">If we become aware that we have collected personal information from a child under 18, we will take steps to delete such information promptly.</p>
                    </div>
                </div>
            </div>

            <!-- Section 11: Contact Us -->
            <div class="privacy-section mb-5" id="section-11">
                <div class="privacy-content-card p-4 rounded shadow-sm">
                    <h3 class="text-gold mb-3">11. Contact Information</h3>
                    
                    <p class="mb-4">For any privacy-related questions, concerns, or requests, please contact us:</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="privacy-contact-method mb-4">
                                <h6 class="text-gold mb-2"><i class="bi bi-building me-2"></i>Luxuria Cars Rental</h6>
                                <p class="mb-1">Shop No 9 - Dr Murad Building</p>
                                <p class="mb-1">Hor Al Anz East - Abu Hail</p>
                                <p class="mb-3">Dubai - United Arab Emirates</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="privacy-contact-method mb-4">
                                <h6 class="text-gold mb-2"><i class="bi bi-envelope me-2"></i>Email</h6>
                                <p class="mb-1"><strong>Privacy Inquiries:</strong> privacy@rentluxuria.com</p>
                                <p class="mb-1"><strong>General:</strong> info@rentluxuria.com</p>
                                <p class="mb-3"><strong>Bookings:</strong> bookings@rentluxuria.com</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="privacy-contact-method mb-4">
                                <h6 class="text-gold mb-2"><i class="bi bi-telephone me-2"></i>Phone</h6>
                                <p class="mb-1">+971 50 271 1549</p>
                                <p class="mb-3">+971 54 270 0030</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="privacy-contact-method mb-4">
                                <h6 class="text-gold mb-2"><i class="bi bi-clock me-2"></i>Response Time</h6>
                                <p class="mb-3">We aim to respond to all privacy inquiries within 30 days.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="privacy-final-notice mt-4 p-4 rounded text-center">
                        <h6 class="text-gold mb-3">Policy Updates</h6>
                        <p class="mb-2">We may update this Privacy Policy from time to time. We will notify you of any material changes by:</p>
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item me-3">• Email notification</li>
                            <li class="list-inline-item me-3">• Website banner</li>
                            <li class="list-inline-item">• Updated "Last Modified" date</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.privacy-hero-section {
    min-height: 320px;
    background: linear-gradient(135deg, #1a1a1a 0%, #bfa133 100%);
    padding: 60px 0 40px 0;
    box-shadow: 0 8px 32px 0 rgba(0,0,0,0.18);
}
.privacy-hero-title {
    font-size: 3.5rem;
    letter-spacing: 0.12em;
    color: #fff;
    text-shadow: 0 2px 12px #0008;
}
.privacy-hero-desc {
    font-size: 1.3rem;
    color: #f7e7c1;
    text-shadow: 0 2px 8px #0005;
}
.text-gold {
    color: #bfa133 !important;
}
.privacy-notice-card {
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    border-left: 4px solid #bfa133;
}
.privacy-toc-card {
    background: #fff;
    border: 1px solid #f0f0f0;
    border-left: 6px solid #bfa133;
}
.privacy-toc-link {
    color: #495057;
    text-decoration: none;
    font-size: 0.9rem;
    display: block;
    padding: 0.4rem 0;
    border-bottom: 1px solid #f8f9fa;
    transition: color 0.3s;
}
.privacy-toc-link:hover {
    color: #bfa133;
    text-decoration: none;
}
.privacy-content-card {
    background: #fff;
    border: 1px solid #f0f0f0;
    border-left: 6px solid #bfa133;
}
.privacy-highlight-box {
    background: #f8f9fa;
    border: 1px solid #bfa133;
}
.privacy-list {
    padding-left: 1.2rem;
}
.privacy-list li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}
.privacy-list-small {
    padding-left: 1rem;
    font-size: 0.9rem;
}
.privacy-list-small li {
    margin-bottom: 0.3rem;
}
.privacy-use-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-top: 3px solid #bfa133;
}
.privacy-important-notice {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 0.5rem;
    padding: 1rem;
}
.privacy-security-item {
    margin-bottom: 1.5rem;
}
.privacy-cookies-table th {
    background: #1a1a1a !important;
    color: #fff;
    border-color: #bfa133;
}
.privacy-cookies-table td {
    border-color: #e9ecef;
}
.privacy-right-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-left: 4px solid #bfa133;
    transition: transform 0.2s, box-shadow 0.2s;
}
.privacy-right-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(191,161,51,0.15);
}
.privacy-contact-rights {
    background: #e7f3ff;
    border: 1px solid #bfa133;
}
.privacy-third-party-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-top: 3px solid #bfa133;
    height: 100%;
}
.privacy-retention-notice {
    background: #d4edda;
    border: 1px solid #c3e6cb;
}
.privacy-transfer-safeguards {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}
.privacy-children-notice {
    background: linear-gradient(45deg, #f8f9fa, #e3f2fd);
    border: 2px solid #bfa133;
}
.privacy-contact-method h6 {
    font-weight: 600;
}
.privacy-final-notice {
    background: linear-gradient(45deg, #1a1a1a, #333);
    color: #fff;
    border: 2px solid #bfa133;
}
.privacy-final-notice .text-gold {
    color: #bfa133 !important;
}
@media (max-width: 991px) {
    .privacy-hero-title {
        font-size: 2.5rem;
    }
    .privacy-hero-section {
        padding: 40px 0 24px 0;
    }
    .privacy-toc-card {
        position: static !important;
        margin-bottom: 2rem;
    }
}
@media (max-width: 767px) {
    .privacy-hero-title {
        font-size: 2rem;
    }
    .privacy-hero-section {
        padding: 30px 0 20px 0;
        min-height: 220px;
    }
    .privacy-content-card {
        padding: 1.5rem !important;
    }
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
