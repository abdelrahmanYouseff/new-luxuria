@extends('layouts.blade_app')

@section('title', 'Coupons - Luxuria UAE')

@section('content')
<div class="modern-coupons-page">
    <!-- Hero Section -->
    <div class="modern-hero-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-12 col-lg-10">
                    <h1 class="modern-hero-title">
                        Special Offers & Discounts
                    </h1>
                    <p class="modern-hero-subtitle">
                        Save more on your luxury car rental experience with our exclusive deals
                    </p>
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
    <div class="modern-coupons-section">
        <div class="container">
            <div class="row g-4 justify-content-center">
                @forelse($formattedCoupons as $coupon)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="modern-coupon-card">
                        <div class="modern-coupon-header">
                            <div class="modern-discount-badge">
                                @if($coupon['discount_type'] === 'percentage')
                                    <span class="discount-value">{{ $coupon['discount_value'] }}%</span>
                                    <span class="discount-text">OFF</span>
                                @else
                                    <span class="discount-value">{{ $coupon['formatted_price'] }}</span>
                                    <span class="discount-text">DISCOUNT</span>
                                @endif
                            </div>
                            <div class="modern-coupon-status">
                                <span class="status-badge">Active</span>
                            </div>
                        </div>

                        <div class="modern-coupon-body">
                            <h3 class="modern-coupon-title">{{ $coupon['name'] }}</h3>
                            <p class="modern-coupon-description">{{ $coupon['description'] }}</p>

                            <div class="modern-coupon-details">
                                <div class="detail-row">
                                    <div class="detail-icon">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">Valid Until</span>
                                        <span class="detail-value">{{ \Carbon\Carbon::parse($coupon['expires_at'])->format('M d, Y') }}</span>
                                    </div>
                                </div>

                                @if($coupon['minimum_amount'] > 0)
                                <div class="detail-row">
                                    <div class="detail-icon">
                                        <i class="bi bi-cash"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">Minimum Spend</span>
                                        <span class="detail-value">{{ $coupon['minimum_amount'] }} AED</span>
                                    </div>
                                </div>
                                @endif

                                @if($coupon['usage_limit'])
                                <div class="detail-row">
                                    <div class="detail-icon">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">Available</span>
                                        <span class="detail-value">{{ $coupon['usage_limit'] - $coupon['used_count'] }} left</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="modern-coupon-footer">
                            <div class="price-section">
                                <span class="price-label">Coupon Price</span>
                                <span class="price-amount">{{ $coupon['price'] }} AED</span>
                            </div>
                            <button class="modern-buy-btn purchase-coupon-btn"
                                    data-coupon-id="{{ $coupon['id'] }}"
                                    data-coupon-name="{{ $coupon['name'] }}"
                                    data-coupon-price="{{ $coupon['price'] }}">
                                <i class="bi bi-bag-plus"></i>
                                Purchase Now
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
/* Modern Professional Design with Black Theme */
.modern-coupons-page {
    margin-top: 80px;
    background: #f8fafc;
    min-height: 100vh;
}

.modern-hero-section {
    background: linear-gradient(135deg, #111 0%, #333 100%);
    padding: 80px 0 60px 0;
    color: white;
    position: relative;
}

.modern-hero-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: #fff;
}

.modern-hero-subtitle {
    font-size: 1.25rem;
    opacity: 0.95;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
    color: #e5e7eb;
}

.modern-coupons-section {
    padding: 80px 0;
}

.modern-coupon-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.modern-coupon-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modern-coupon-header {
    padding: 24px 24px 0 24px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.modern-discount-badge {
    background: linear-gradient(135deg, #111 0%, #333 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 12px;
    text-align: center;
    min-width: 100px;
}

.discount-value {
    display: block;
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1;
}

.discount-text {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 4px;
}

.modern-coupon-status {
    padding: 8px 16px;
    background: #dcfce7;
    color: #166534;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.modern-coupon-body {
    padding: 24px;
    flex-grow: 1;
}

.modern-coupon-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111;
    margin-bottom: 12px;
    line-height: 1.3;
}

.modern-coupon-description {
    color: #64748b;
    margin-bottom: 24px;
    line-height: 1.6;
}

.modern-coupon-details {
    space-y: 16px;
}

.detail-row {
    display: flex;
    align-items: center;
    margin-bottom: 16px;
}

.detail-icon {
    width: 40px;
    height: 40px;
    background: #f1f5f9;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
    color: #111;
}

.detail-content {
    flex: 1;
}

.detail-label {
    display: block;
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 2px;
}

.detail-value {
    display: block;
    font-weight: 600;
    color: #111;
}

.modern-coupon-footer {
    padding: 24px;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
}

.price-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.price-label {
    color: #64748b;
    font-size: 0.875rem;
}

.price-amount {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111;
}

.modern-buy-btn {
    width: 100%;
    background: linear-gradient(135deg, #111 0%, #333 100%);
    color: white;
    border: none;
    padding: 14px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.modern-buy-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    background: linear-gradient(135deg, #000 0%, #111 100%);
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
    const purchaseButtons = document.querySelectorAll('.purchase-coupon-btn');

    purchaseButtons.forEach(button => {
        button.addEventListener('click', async function() {
            // Check if user is authenticated
            @auth
                const couponId = this.dataset.couponId;
                const couponName = this.dataset.couponName;
                const couponPrice = parseFloat(this.dataset.couponPrice);

                // Show loading state
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
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
                    this.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Error!';
                    this.style.background = '#dc3545';
                    this.style.color = 'white';

                    // Reset button after 3 seconds
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.style.background = '';
                        this.style.color = '';
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
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
