@extends('layouts.blade_app')

@section('title', 'Simple Stripe Test')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>💳 Simple Stripe Test</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Test Configuration:</strong><br>
                        • CSRF Token: <span id="csrf-status">Checking...</span><br>
                        • Stripe Public Key: <span id="stripe-status">Checking...</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Test Amount (SAR)</label>
                        <input type="number" id="testAmount" class="form-control" value="50" min="1">
                    </div>

                    <button id="testPayment" class="btn btn-success w-100">
                        <i class="bi bi-credit-card"></i> Test Payment
                    </button>

                    <div id="result" class="mt-3 p-3 bg-light rounded"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const testPaymentBtn = document.getElementById('testPayment');
    const testAmount = document.getElementById('testAmount');
    const result = document.getElementById('result');
    const csrfStatus = document.getElementById('csrf-status');
    const stripeStatus = document.getElementById('stripe-status');

    // Check CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        csrfStatus.innerHTML = '✅ Found';
        csrfStatus.style.color = 'green';
    } else {
        csrfStatus.innerHTML = '❌ Not Found';
        csrfStatus.style.color = 'red';
    }

    // Check Stripe public key
    fetch('/stripe/public-key')
        .then(response => response.json())
        .then(data => {
            if (data.public_key) {
                stripeStatus.innerHTML = '✅ Found';
                stripeStatus.style.color = 'green';
            } else {
                stripeStatus.innerHTML = '❌ Not Found';
                stripeStatus.style.color = 'red';
            }
        })
        .catch(error => {
            stripeStatus.innerHTML = '❌ Error: ' + error.message;
            stripeStatus.style.color = 'red';
        });

    function showResult(message, type = 'info') {
        const color = type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1';
        result.style.backgroundColor = color;
        result.innerHTML = message;
    }

    testPaymentBtn.addEventListener('click', async function() {
        const amount = parseFloat(testAmount.value);

        if (isNaN(amount) || amount <= 0) {
            showResult('Please enter a valid amount', 'error');
            return;
        }

        this.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
        this.disabled = true;

        try {
            console.log('Testing payment with amount:', amount);

            // Create payment intent
            const response = await fetch('/stripe/create-payment-intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    coupon_id: 999,
                    amount: amount,
                    coupon_name: 'Test Coupon'
                })
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                showResult(`
                    <strong>✅ Payment Intent Created!</strong><br>
                    Payment Intent ID: ${data.payment_intent_id}<br>
                    Client Secret: ${data.client_secret.substring(0, 20)}...
                `, 'success');
            } else {
                showResult(`<strong>❌ Error:</strong> ${data.error}`, 'error');
            }
        } catch (error) {
            console.error('Payment error:', error);
            showResult(`<strong>❌ Network Error:</strong> ${error.message}`, 'error');
        } finally {
            this.innerHTML = '<i class="bi bi-credit-card"></i> Test Payment';
            this.disabled = false;
        }
    });
});
</script>
@endsection
