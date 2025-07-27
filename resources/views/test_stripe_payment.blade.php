@extends('layouts.blade_app')

@section('title', 'Test Stripe Payment')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>💳 Test Stripe Payment Integration</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Stripe Configuration:</strong><br>
                        • Public Key: {{ config('services.stripe.public_key') ? '✅ Set' : '❌ Not Set' }}<br>
                        • Secret Key: {{ config('services.stripe.secret_key') ? '✅ Set' : '❌ Not Set' }}<br>
                        • Environment: Test Mode
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Test Payment Intent</h5>
                                </div>
                                <div class="card-body">
                                    <button id="testPaymentIntent" class="btn btn-success">
                                        <i class="bi bi-credit-card"></i> Test Payment Intent
                                    </button>
                                    <div id="paymentIntentResult" class="mt-3 p-3 bg-light rounded"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Test Checkout Session</h5>
                                </div>
                                <div class="card-body">
                                    <button id="testCheckoutSession" class="btn btn-warning">
                                        <i class="bi bi-cart"></i> Test Checkout Session
                                    </button>
                                    <div id="checkoutResult" class="mt-3 p-3 bg-light rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Test Coupon Purchase:</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6>Test Coupon 1</h6>
                                        <p class="text-muted">50 SAR</p>
                                        <button class="btn btn-primary btn-sm test-purchase" data-coupon-id="1" data-amount="50" data-name="Test Coupon 1">
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6>Test Coupon 2</h6>
                                        <p class="text-muted">100 SAR</p>
                                        <button class="btn btn-primary btn-sm test-purchase" data-coupon-id="2" data-amount="100" data-name="Test Coupon 2">
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6>Test Coupon 3</h6>
                                        <p class="text-muted">200 SAR</p>
                                        <button class="btn btn-primary btn-sm test-purchase" data-coupon-id="3" data-amount="200" data-name="Test Coupon 3">
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const testPaymentIntentBtn = document.getElementById('testPaymentIntent');
    const testCheckoutSessionBtn = document.getElementById('testCheckoutSession');
    const paymentIntentResult = document.getElementById('paymentIntentResult');
    const checkoutResult = document.getElementById('checkoutResult');
    const testPurchaseBtns = document.querySelectorAll('.test-purchase');

    function showResult(element, message, type = 'info') {
        const color = type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1';
        element.style.backgroundColor = color;
        element.innerHTML = message;
    }

    testPaymentIntentBtn.addEventListener('click', async function() {
        showResult(paymentIntentResult, 'Testing payment intent...');

        try {
            const response = await fetch('/stripe/create-payment-intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    coupon_id: 1,
                    amount: 50,
                    coupon_name: 'Test Coupon'
                })
            });

            const data = await response.json();

            if (data.success) {
                showResult(paymentIntentResult, `
                    <strong>✅ Payment Intent Created!</strong><br>
                    Payment Intent ID: ${data.payment_intent_id}<br>
                    Client Secret: ${data.client_secret.substring(0, 20)}...
                `, 'success');
            } else {
                showResult(paymentIntentResult, `<strong>❌ Error:</strong> ${data.error}`, 'error');
            }
        } catch (error) {
            showResult(paymentIntentResult, `<strong>❌ Network Error:</strong> ${error.message}`, 'error');
        }
    });

    testCheckoutSessionBtn.addEventListener('click', async function() {
        showResult(checkoutResult, 'Testing checkout session...');

        try {
            // First create payment intent
            const paymentResponse = await fetch('/stripe/create-payment-intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    coupon_id: 1,
                    amount: 50,
                    coupon_name: 'Test Coupon'
                })
            });

            const paymentData = await paymentResponse.json();

            if (paymentData.success) {
                // Then create checkout session
                const checkoutResponse = await fetch('/stripe/create-checkout-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        coupon_id: 1,
                        amount: 50,
                        coupon_name: 'Test Coupon',
                        payment_intent_id: paymentData.payment_intent_id
                    })
                });

                const checkoutData = await checkoutResponse.json();

                if (checkoutData.success) {
                    showResult(checkoutResult, `
                        <strong>✅ Checkout Session Created!</strong><br>
                        Session ID: ${checkoutData.session_id}<br>
                        <a href="${checkoutData.url}" target="_blank" class="btn btn-success btn-sm mt-2">
                            <i class="bi bi-arrow-right"></i> Go to Checkout
                        </a>
                    `, 'success');
                } else {
                    showResult(checkoutResult, `<strong>❌ Error:</strong> ${checkoutData.error}`, 'error');
                }
            } else {
                showResult(checkoutResult, `<strong>❌ Payment Intent Error:</strong> ${paymentData.error}`, 'error');
            }
        } catch (error) {
            showResult(checkoutResult, `<strong>❌ Network Error:</strong> ${error.message}`, 'error');
        }
    });

    testPurchaseBtns.forEach(button => {
        button.addEventListener('click', async function() {
            const couponId = this.dataset.couponId;
            const amount = parseFloat(this.dataset.amount);
            const name = this.dataset.name;

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
                        amount: amount,
                        coupon_name: name
                    })
                });

                const paymentData = await paymentResponse.json();

                if (paymentData.success) {
                    // Create checkout session
                    const checkoutResponse = await fetch('/stripe/create-checkout-session', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            coupon_id: couponId,
                            amount: amount,
                            coupon_name: name,
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
                this.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Error!';
                this.style.background = '#dc3545';
                this.style.color = 'white';

                setTimeout(() => {
                    this.innerHTML = 'Buy Now';
                    this.style.background = '';
                    this.style.color = '';
                    this.disabled = false;
                }, 3000);
            }
        });
    });
});
</script>
@endsection
