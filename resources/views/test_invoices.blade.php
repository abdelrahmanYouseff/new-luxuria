@extends('layouts.blade_app')

@section('title', 'Test Invoices')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>🧾 Test Coupon Invoices System</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Current User:</strong> {{ Auth::user()->name }} ({{ Auth::user()->email }})<br>
                        <strong>User ID:</strong> {{ Auth::user()->id }}
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Create Test Invoice</h5>
                                </div>
                                <div class="card-body">
                                    <button id="createTestInvoice" class="btn btn-success">
                                        <i class="bi bi-plus-circle"></i> Create Test Invoice
                                    </button>
                                    <div id="createResult" class="mt-3 p-3 bg-light rounded"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Get User Invoices</h5>
                                </div>
                                <div class="card-body">
                                    <button id="getUserInvoices" class="btn btn-info">
                                        <i class="bi bi-list"></i> Get My Invoices
                                    </button>
                                    <div id="invoicesResult" class="mt-3 p-3 bg-light rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Test Payment Flow:</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h6>Test Coupon 1</h6>
                                        <p class="text-muted">50 SAR</p>
                                        <button class="btn btn-primary btn-sm test-purchase" data-coupon-id="1" data-amount="50" data-name="Test Coupon 1">
                                            Buy & Create Invoice
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
                                            Buy & Create Invoice
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
                                            Buy & Create Invoice
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="purchaseResult" class="mt-3 p-3 bg-light rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const createTestInvoiceBtn = document.getElementById('createTestInvoice');
    const getUserInvoicesBtn = document.getElementById('getUserInvoices');
    const createResult = document.getElementById('createResult');
    const invoicesResult = document.getElementById('invoicesResult');
    const purchaseResult = document.getElementById('purchaseResult');
    const testPurchaseBtns = document.querySelectorAll('.test-purchase');

    function showResult(element, message, type = 'info') {
        const color = type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1';
        element.style.backgroundColor = color;
        element.innerHTML = message;
    }

    createTestInvoiceBtn.addEventListener('click', async function() {
        showResult(createResult, 'Creating test invoice...');

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
                    coupon_id: 999,
                    amount: 50,
                    coupon_name: 'Test Invoice Coupon'
                })
            });

            const paymentData = await paymentResponse.json();

            if (paymentData.success) {
                showResult(createResult, `
                    <strong>✅ Test Invoice Created!</strong><br>
                    Payment Intent ID: ${paymentData.payment_intent_id}<br>
                    Invoice Number: ${paymentData.invoice_number}<br>
                    Amount: 50 AED
                `, 'success');
            } else {
                showResult(createResult, `<strong>❌ Error:</strong> ${paymentData.error}`, 'error');
            }
        } catch (error) {
            showResult(createResult, `<strong>❌ Network Error:</strong> ${error.message}`, 'error');
        }
    });

    getUserInvoicesBtn.addEventListener('click', async function() {
        showResult(invoicesResult, 'Loading invoices...');

        try {
            const response = await fetch('/stripe/user-invoices', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.success) {
                if (data.invoices.length === 0) {
                    showResult(invoicesResult, '<strong>📭 No invoices found</strong><br>Try creating a test invoice first.', 'info');
                } else {
                    const invoicesList = data.invoices.map(invoice =>
                        `• ${invoice.invoice_number} - ${invoice.coupon_name} (${invoice.amount} ${invoice.currency}) - ${invoice.payment_status}`
                    ).join('<br>');
                    showResult(invoicesResult, `
                        <strong>✅ Found ${data.invoices.length} invoice(s):</strong><br>
                        ${invoicesList}
                    `, 'success');
                }
            } else {
                showResult(invoicesResult, `<strong>❌ Error:</strong> ${data.error}`, 'error');
            }
        } catch (error) {
            showResult(invoicesResult, `<strong>❌ Network Error:</strong> ${error.message}`, 'error');
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
                showResult(purchaseResult, `Creating payment intent for ${name}...`);

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
                    showResult(purchaseResult, `
                        <strong>✅ Payment Intent Created!</strong><br>
                        Coupon: ${name}<br>
                        Amount: ${amount} AED<br>
                        Payment Intent ID: ${paymentData.payment_intent_id}<br>
                        Invoice Number: ${paymentData.invoice_number}<br>
                        <small class="text-muted">Invoice has been created in the database</small>
                    `, 'success');
                } else {
                    throw new Error(paymentData.error || 'Payment failed');
                }
            } catch (error) {
                console.error('Purchase error:', error);
                showResult(purchaseResult, `<strong>❌ Error:</strong> ${error.message}`, 'error');
            } finally {
                this.innerHTML = 'Buy & Create Invoice';
                this.disabled = false;
            }
        });
    });

    // Auto-load invoices on page load
    setTimeout(() => {
        getUserInvoicesBtn.click();
    }, 1000);
});
</script>
@endsection
