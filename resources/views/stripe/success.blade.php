@extends('layouts.blade_app')

@section('title', 'Payment Successful')

@section('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection

@section('content')
<div class="payment-success-page">
    <div class="success-wrapper">
        <!-- Main Success Card -->
        <div class="success-card">
            <div class="success-header">
                <div class="success-icon-wrapper">
                    @if($success ?? true)
                        <svg class="success-check" viewBox="0 0 52 52">
                            <circle class="success-check-circle" cx="26" cy="26" r="25" fill="none"/>
                            <path class="success-check-mark" fill="none" d="m14.1 27.2l7.1 7.2 16.7-16.8"/>
                        </svg>
                    @else
                        <div class="error-icon">
                            <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #dc3545;"></i>
                        </div>
                    @endif
                </div>
                <h1 class="success-title">
                    @if($success ?? true)
                        Payment Successful
                    @else
                        Payment Error
                    @endif
                </h1>
                <p class="success-message">
                    {{ $message ?? 'Your transaction has been processed successfully' }}
                </p>
            </div>

            @if($success ?? true)
            <div class="transaction-details">
                <!-- Transaction Complete -->
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div class="detail-text">
                        <h3>Transaction Complete</h3>
                        <p>Your payment has been confirmed
                        @if(isset($invoice_number))
                            - Invoice #{{ $invoice_number }}
                        @endif
                        </p>
                    </div>
                </div>

                <!-- Purchase Details -->
                @if(isset($coupon_name) && isset($amount))
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                    <div class="detail-text">
                        <h3>{{ $coupon_name }} - Purchased</h3>
                        <p>Amount: {{ $amount }} AED</p>
                    </div>
                </div>
                @endif

                <!-- Points Reward -->
                @if(($points_added ?? 0) > 0)
                <div class="detail-item points-item">
                    <div class="detail-icon points-icon">
                        <i class="bi bi-gem"></i>
                    </div>
                    <div class="detail-text">
                        <h3>🎁 Bonus Points Earned!</h3>
                        <p><strong>{{ $points_added }} points</strong> added to your account</p>
                    </div>
                </div>
                @endif

                <!-- Email Confirmation -->
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="detail-text">
                        <h3>Email Confirmation</h3>
                        <p>Receipt sent to your email address</p>
                    </div>
                </div>

                <!-- User Info -->
                @if(isset($user_name))
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="detail-text">
                        <h3>Customer</h3>
                        <p>{{ $user_name }}</p>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <div class="action-section">
                <h2 class="action-title">What would you like to do next?</h2>
                <div class="action-grid">
                    <a href="/coupons" class="action-btn primary">
                        <i class="bi bi-arrow-left-circle"></i>
                        <span>Browse More Coupons</span>
                    </a>
                    <a href="/" class="action-btn secondary">
                        <i class="bi bi-house-door"></i>
                        <span>Return Home</span>
                    </a>
                    <a href="/invoice-coupons" class="action-btn tertiary">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>View My Invoices</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="footer-note">
            <p>Need assistance? <a href="/contact" class="support-link">Contact our support team</a></p>
        </div>
    </div>
</div>

<style>
.payment-success-page {
    min-height: 100vh;
    background: #fff;
    padding: 100px 20px 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.success-wrapper {
    max-width: 600px;
    width: 100%;
}

.success-card {
    background: #fff;
    border: 1.5px solid #f3e7c1;
    border-radius: 24px;
    padding: 50px 40px;
    box-shadow: 0 12px 40px rgba(191,161,51,0.10), 0 1.5px 0 #f3e7c1;
    position: relative;
    overflow: hidden;
}
.success-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #bfa133, transparent);
}

.success-header {
    text-align: center;
    margin-bottom: 40px;
}
.success-icon-wrapper {
    margin-bottom: 24px;
}
.success-check {
    width: 80px;
    height: 80px;
    margin: 0 auto;
}
.success-check-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #bfa133;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}
.success-check-mark {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    stroke-width: 3;
    stroke-miterlimit: 10;
    stroke: #bfa133;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}
@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}
.success-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #222;
    margin-bottom: 12px;
    letter-spacing: -0.02em;
    font-family: 'Playfair Display', serif;
}
.success-message {
    color: #666;
    font-size: 1.1rem;
    margin: 0;
}
.transaction-details {
    margin-bottom: 40px;
}
.detail-item {
    display: flex;
    align-items: center;
    padding: 20px 0;
    border-bottom: 1px solid #f3e7c1;
}
.detail-item:last-child {
    border-bottom: none;
}
.detail-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #bfa133, #d4b846);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    color: #fff;
    font-size: 1.3rem;
}
.detail-text h3 {
    color: #222;
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0 0 4px 0;
}
.detail-text p {
    color: #666;
    margin: 0;
    font-size: 0.95rem;
}
.points-item {
    background: linear-gradient(135deg, #fffbe6, #f7ecd0);
    border: 1px solid #bfa13333;
    border-radius: 12px;
    padding: 24px 20px;
    position: relative;
    overflow: hidden;
}
.points-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #bfa133, transparent);
}
.points-icon {
    background: linear-gradient(135deg, #ffd700, #bfa133);
    animation: pulse-gold 2s infinite;
}
@keyframes pulse-gold {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 10px rgba(191, 161, 51, 0.13);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(191, 161, 51, 0.22);
    }
}
.points-item .detail-text h3 {
    color: #bfa133;
    font-weight: 800;
}
.points-item .detail-text p {
    color: #222;
    font-size: 1rem;
}
.points-item .detail-text strong {
    color: #bfa133;
    font-size: 1.1rem;
    font-weight: 700;
}
.action-section {
    text-align: center;
}
.action-title {
    color: #222;
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 30px;
    font-family: 'Playfair Display', serif;
}
.action-grid {
    display: flex;
    gap: 16px;
    flex-direction: column;
}
.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 16px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.3s ease;
    border: 1px solid transparent;
    font-size: 1.08rem;
}
.action-btn.primary {
    background: linear-gradient(135deg, #bfa133, #d4b846);
    color: #fff;
}
.action-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(191, 161, 51, 0.13);
    color: #fff;
}
.action-btn.secondary {
    background: #f3e7c1;
    color: #222;
    border-color: #f3e7c1;
}
.action-btn.secondary:hover {
    background: #fffbe6;
    color: #222;
    transform: translateY(-2px);
}
.action-btn.tertiary {
    background: transparent;
    color: #bfa133;
    border-color: #bfa13333;
}
.action-btn.tertiary:hover {
    background: #fffbe6;
    color: #bfa133;
    border-color: #bfa13377;
    transform: translateY(-2px);
}
.footer-note {
    text-align: center;
    margin-top: 30px;
    padding: 20px;
    background: #fffbe6;
    border-radius: 12px;
    border: 1px solid #f3e7c1;
}
.footer-note p {
    color: #666;
    margin: 0;
}
.support-link {
    color: #bfa133;
    text-decoration: none;
    font-weight: 600;
}
.support-link:hover {
    color: #d4b846;
    text-decoration: underline;
}
@media (max-width: 768px) {
    .payment-success-page {
        padding: 80px 15px 30px;
    }
    .success-card {
        padding: 40px 25px;
    }
    .success-title {
        font-size: 2rem;
    }
    .detail-item {
        padding: 16px 0;
    }
    .detail-icon {
        width: 45px;
        height: 45px;
        margin-right: 16px;
        font-size: 1.1rem;
    }
    .action-grid {
        gap: 12px;
    }
    .action-btn {
        padding: 14px 20px;
        font-size: 0.95rem;
    }
}
@media (max-width: 480px) {
    .success-title {
        font-size: 1.8rem;
    }
    .success-check {
        width: 60px;
        height: 60px;
    }
    .detail-item {
        flex-direction: column;
        text-align: center;
        padding: 20px 0;
    }
    .detail-icon {
        margin-right: 0;
        margin-bottom: 12px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const sessionId = urlParams.get('session_id');

    if (sessionId) {
        console.log('Payment session ID:', sessionId);
    }

    // Add entrance animation to cards
    const detailItems = document.querySelectorAll('.detail-item');
    detailItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        item.style.transition = 'all 0.6s ease';

        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 200 + (index * 150));
    });

    // Add animation to action buttons
    const actionBtns = document.querySelectorAll('.action-btn');
    actionBtns.forEach((btn, index) => {
        btn.style.opacity = '0';
        btn.style.transform = 'translateY(20px)';
        btn.style.transition = 'all 0.5s ease';

        setTimeout(() => {
            btn.style.opacity = '1';
            btn.style.transform = 'translateY(0)';
        }, 800 + (index * 100));
    });

    // Special animation for points item
    const pointsItem = document.querySelector('.points-item');
    if (pointsItem) {
        pointsItem.style.opacity = '0';
        pointsItem.style.transform = 'scale(0.9)';
        pointsItem.style.transition = 'all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';

        setTimeout(() => {
            pointsItem.style.opacity = '1';
            pointsItem.style.transform = 'scale(1)';

            // Add confetti effect for points
            setTimeout(() => {
                createConfetti();
            }, 300);
        }, 1200);
    }

    // Simple confetti effect
    function createConfetti() {
        const colors = ['#ffd700', '#bfa133', '#ffffff', '#d4b846'];
        const confettiCount = 50;

        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.style.position = 'fixed';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.top = '-10px';
            confetti.style.width = '4px';
            confetti.style.height = '4px';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.pointerEvents = 'none';
            confetti.style.borderRadius = '50%';
            confetti.style.zIndex = '9999';

            document.body.appendChild(confetti);

            const animation = confetti.animate([
                {
                    transform: 'translateY(0) rotate(0deg)',
                    opacity: 1
                },
                {
                    transform: `translateY(100vh) rotate(${Math.random() * 360}deg)`,
                    opacity: 0
                }
            ], {
                duration: Math.random() * 2000 + 1000,
                easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
            });

            animation.onfinish = () => {
                document.body.removeChild(confetti);
            };
        }
    }
});
</script>
@endsection

