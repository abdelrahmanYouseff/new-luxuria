@extends('layouts.blade_app')

@section('title', 'Coupons - Luxuria UAE')

@section('content')
<div class="luxury-coupons-page">
    <!-- Hero Section -->
    <div class="luxury-hero-section">
        <div class="hero-background">
            <div class="hero-pattern"></div>
            <div class="hero-overlay"></div>
        </div>
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-12 col-lg-10">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="bi bi-star-fill"></i>
                            <span>Exclusive Offers</span>
                        </div>
                        <h1 class="luxury-hero-title">
                            Premium Discounts & Offers
                        </h1>
                        <p class="luxury-hero-subtitle">
                            Unlock exclusive savings on luxury car rentals with our carefully curated collection of premium coupons
                        </p>
                        <div class="hero-stats">
                            <div class="stat-item">
                                <span class="stat-number">{{ count($formattedCoupons) }}</span>
                                <span class="stat-label">Active Offers</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">24/7</span>
                                <span class="stat-label">Available</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">100%</span>
                                <span class="stat-label">Secure</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testing Mode Notice -->
    @if(config('services.stripe.secret_key') && str_contains(config('services.stripe.secret_key'), 'your_secret_key_here'))
    <div class="testing-mode-notice">
        <div class="container">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                <strong>وضع الاختبار:</strong> النظام يعمل حالياً في وضع الاختبار - لا حاجة لمفاتيح Stripe حقيقية
            </div>
        </div>
    </div>
    @endif

    <!-- Coupons Grid -->
    <div class="luxury-coupons-section">
        <div class="container">
            <!-- Section Header -->
            <div class="section-header text-center mb-5">
                <h2 class="section-title">Available Offers</h2>
                <p class="section-subtitle">Choose from our premium collection of exclusive discounts</p>
            </div>

            <!-- Filters -->
            <div class="coupons-filters mb-4">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="filter-tabs">
                            <button class="filter-tab active" data-filter="all">All Offers</button>
                            <button class="filter-tab" data-filter="percentage">Percentage</button>
                            <button class="filter-tab" data-filter="fixed">Fixed Amount</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                @forelse($formattedCoupons as $coupon)
                <div class="col-12 col-md-6 col-lg-4" data-coupon-type="{{ $coupon['discount_type'] }}">
                    <div class="luxury-coupon-card">

                        <!-- Coupon Header -->
                        <div class="luxury-coupon-header">
                            <div class="coupon-status">
                                <div class="status-indicator active"></div>
                                <span class="status-text">Active</span>
                            </div>
                        </div>

                        <!-- Coupon Body -->
                        <div class="luxury-coupon-body">
                            <div class="coupon-title-section">
                                <h3 class="luxury-coupon-title">{{ $coupon['name'] }}</h3>
                                <div class="coupon-category">
                                    <i class="bi bi-tag-fill"></i>
                                    <span>{{ ucfirst($coupon['discount_type']) }} Discount</span>
                                </div>
                            </div>

                            <p class="luxury-coupon-description">{{ $coupon['description'] }}</p>

                            <!-- Coupon Features -->
                            <div class="coupon-features">
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="feature-content">
                                        <span class="feature-label">Valid Until</span>
                                        <span class="feature-value">{{ \Carbon\Carbon::parse($coupon['expires_at'])->format('M d, Y') }}</span>
                                    </div>
                                </div>

                                @if($coupon['minimum_amount'] > 0)
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
                                    <div class="feature-content">
                                        <span class="feature-label">Minimum Spend</span>
                                        <span class="feature-value">{{ $coupon['minimum_amount'] }} AED</span>
                                    </div>
                                </div>
                                @endif

                                @if($coupon['usage_limit'])
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div class="feature-content">
                                        <span class="feature-label">Available</span>
                                        <span class="feature-value">{{ $coupon['usage_limit'] - $coupon['used_count'] }} left</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Coupon Footer -->
                        <div class="luxury-coupon-footer">
                            <div class="pricing-section">
                                <div class="price-info">
                                    <span class="price-label">Coupon Price</span>
                                    <span class="price-value">{{ $coupon['price'] }} AED</span>
                                </div>
                                <div class="savings-info">
                                    <span class="savings-label">Potential Savings</span>
                                    <span class="savings-value">
                                        @if($coupon['discount_type'] === 'percentage')
                                            Up to {{ $coupon['discount_value'] }}% off
                                        @else
                                            Up to {{ $coupon['formatted_price'] }} off
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <button class="luxury-purchase-btn purchase-coupon-btn"
                                    data-coupon-id="{{ $coupon['id'] }}"
                                    data-coupon-name="{{ $coupon['name'] }}"
                                    data-coupon-price="{{ $coupon['price'] }}">
                                <span class="btn-content">
                                    <i class="bi bi-bag-plus"></i>
                                    <span>Purchase Now</span>
                                </span>
                                <div class="btn-loader" style="display: none;">
                                    <div class="spinner"></div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <div class="modern-empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-ticket-perforated"></i>
                        </div>
                        <h3 class="empty-title">No Offers Available</h3>
                        <p class="empty-subtitle">Check back soon for exciting new deals and discounts!</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="how-it-works-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 text-center">
                    <h2 class="section-title">How It Works</h2>
                    <p class="section-subtitle">Simple steps to save on your luxury car rental</p>

                    <div class="steps-grid">
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Choose Your Coupon</h4>
                                <p>Browse and select the perfect discount for your needs</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Complete Purchase</h4>
                                <p>Securely purchase your selected coupon online</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Book & Save</h4>
                                <p>Apply your coupon when booking to enjoy instant savings</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Luxury Professional Design with Black Theme */
.luxury-coupons-page {
    margin-top: 80px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
}

/* Hero Section */
.luxury-hero-section {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    padding: 100px 0 80px 0;
    color: white;
    position: relative;
    overflow: hidden;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image:
        radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
    background-size: 300px 300px, 500px 500px;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(26,26,26,0.9) 0%, rgba(45,45,45,0.95) 100%);
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 24px;
}

.hero-badge i {
    color: #ffd700;
}

.luxury-hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    color: #fff;
    line-height: 1.2;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.luxury-hero-subtitle {
    font-size: 1.25rem;
    opacity: 0.9;
    max-width: 700px;
    margin: 0 auto 40px auto;
    line-height: 1.6;
    color: #e5e7eb;
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 60px;
    margin-top: 40px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 800;
    color: #ffd700;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 0.9rem;
    color: #ccc;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Coupons Section */
.luxury-coupons-section {
    padding: 80px 0;
}

.section-header {
    margin-bottom: 60px;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 16px;
}

.section-subtitle {
    font-size: 1.125rem;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto;
}

/* Filters */
.coupons-filters {
    margin-bottom: 40px;
}

.filter-tabs {
    display: flex;
    background: white;
    border-radius: 12px;
    padding: 4px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

.filter-tab {
    flex: 1;
    background: transparent;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 500;
    color: #64748b;
    transition: all 0.3s ease;
    cursor: pointer;
}

.filter-tab.active {
    background: #1a1a1a;
    color: white;
    box-shadow: 0 2px 8px rgba(26, 26, 26, 0.3);
}

.filter-tab:hover:not(.active) {
    background: #f1f5f9;
    color: #1a1a1a;
}

/* Coupon Cards */
.luxury-coupon-card {
    background: #1a1a1a;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    transition: all 0.4s ease;
    border: 1px solid #333;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
}

.luxury-coupon-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border-color: #e2e8f0;
}



/* Coupon Header */
.luxury-coupon-header {
    padding: 30px 30px 0 30px;
    display: flex;
    justify-content: flex-end;
    align-items: flex-start;
}

.coupon-status {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #dcfce7;
    color: #166534;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #22c55e;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Coupon Body */
.luxury-coupon-body {
    padding: 30px;
    flex-grow: 1;
}

.coupon-title-section {
    margin-bottom: 20px;
}

.luxury-coupon-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 8px;
    line-height: 1.3;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.coupon-category {
    display: flex;
    align-items: center;
    gap: 6px;
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

.coupon-category i {
    color: white;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

.luxury-coupon-description {
    color: white;
    margin-bottom: 30px;
    line-height: 1.6;
    font-size: 0.95rem;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

/* Coupon Features */
.coupon-features {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 16px;
}

.feature-icon {
    width: 45px;
    height: 45px;
    background: #333;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    border: 1px solid #444;
}

.feature-content {
    flex: 1;
}

.feature-label {
    display: block;
    font-size: 0.875rem;
    color: white;
    margin-bottom: 4px;
    font-weight: 500;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

.feature-value {
    display: block;
    font-weight: 600;
    color: white;
    font-size: 0.95rem;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

/* Coupon Footer */
.luxury-coupon-footer {
    padding: 30px;
    border-top: 1px solid #333;
    background: #2d2d2d;
}

.pricing-section {
    margin-bottom: 20px;
}

.price-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.price-label {
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

.price-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: white;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.savings-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.savings-label {
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

.savings-value {
    font-size: 1rem;
    font-weight: 600;
    color: #22c55e;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

/* Purchase Button */
.luxury-purchase-btn {
    width: 100%;
    background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
    color: #1a1a1a;
    border: none;
    padding: 16px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.luxury-purchase-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.luxury-purchase-btn:hover::before {
    left: 100%;
}

.luxury-purchase-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    background: linear-gradient(135deg, #f4d03f 0%, #d4af37 100%);
}

.btn-content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-loader {
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.modern-empty-state {
    padding: 80px 20px;
}

.empty-icon {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 24px;
}

.empty-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #111;
    margin-bottom: 12px;
}

.empty-subtitle {
    color: #64748b;
    font-size: 1.125rem;
}

.how-it-works-section {
    background: white;
    padding: 80px 0;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #111;
    margin-bottom: 16px;
}

.section-subtitle {
    font-size: 1.25rem;
    color: #64748b;
    margin-bottom: 60px;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 40px;
    margin-top: 60px;
}

.step-item {
    text-align: center;
}

.step-number {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #111 0%, #333 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 auto 24px auto;
}

.step-content h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111;
    margin-bottom: 12px;
}

.step-content p {
    color: #64748b;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-hero-title {
        font-size: 2rem;
    }

    .modern-hero-subtitle {
        font-size: 1.125rem;
    }

    .modern-coupons-section {
        padding: 60px 0;
    }

    .section-title {
        font-size: 2rem;
    }

    .steps-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const couponCards = document.querySelectorAll('[data-coupon-type]');

    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.dataset.filter;

            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Filter coupons
            couponCards.forEach(card => {
                if (filter === 'all' || card.dataset.couponType === filter) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Purchase functionality
    const purchaseButtons = document.querySelectorAll('.purchase-coupon-btn');

    purchaseButtons.forEach(button => {
        button.addEventListener('click', async function() {
            // Check if user is authenticated
            @auth
                const couponId = this.dataset.couponId;
                const couponName = this.dataset.couponName;
                const couponPrice = parseFloat(this.dataset.couponPrice);

                // Show loading state
                const btnContent = this.querySelector('.btn-content');
                const btnLoader = this.querySelector('.btn-loader');

                btnContent.style.display = 'none';
                btnLoader.style.display = 'flex';
                this.disabled = true;

                try {
                    // Create payment intent
                    const paymentResponse = await fetch('/stripe/create-payment-intent', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            coupon_id: couponId,
                            amount: couponPrice,
                            coupon_name: couponName
                        })
                    });

                    const paymentData = await paymentResponse.json();

                    if (paymentData.success) {
                        // Check if using mock payment system
                        if (paymentData.is_mock && paymentData.mock_payment_url) {
                            // Redirect to mock payment page
                            window.location.href = paymentData.mock_payment_url;
                            return;
                        }

                        // Create checkout session for real Stripe
                        const checkoutResponse = await fetch('/stripe/create-checkout-session', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                coupon_id: couponId,
                                amount: couponPrice,
                                coupon_name: couponName,
                                payment_intent_id: paymentData.payment_intent_id
                            })
                        });

                        const checkoutData = await checkoutResponse.json();

                        if (checkoutData.success) {
                            // Redirect to Stripe Checkout
                            window.location.href = checkoutData.url;
                        } else {
                            throw new Error(checkoutData.error || 'Checkout failed');
                        }
                    } else {
                        throw new Error(paymentData.error || 'Payment failed');
                    }
                } catch (error) {
                    console.error('Purchase error:', error);

                    // Show error state
                    btnContent.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Error!';
                    btnContent.style.display = 'flex';
                    btnLoader.style.display = 'none';
                    this.style.background = '#dc3545';

                    // Reset button after 3 seconds
                    setTimeout(() => {
                        btnContent.innerHTML = '<i class="bi bi-bag-plus"></i><span>Purchase Now</span>';
                        this.style.background = '';
                        this.disabled = false;
                    }, 3000);

                    // Show user-friendly error message
                    alert('حدث خطأ أثناء معالجة الدفع. يرجى المحاولة مرة أخرى.');
                }
            @else
                // User not authenticated - redirect to login
                alert('يجب تسجيل الدخول أولاً لشراء الكوبونات');
                window.location.href = '/login';
            @endauth
        });
    });

    // Add smooth animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe coupon cards
    couponCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
