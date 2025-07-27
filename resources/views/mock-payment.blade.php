<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>صفحة الدفع التجريبية - Mock Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .btn-payment {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            color: white;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40,167,69,0.3);
            color: white;
        }
        .mock-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .amount-display {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .loading {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-6">
            <div class="payment-card">
                <!-- Mock Notice -->
                <div class="mock-notice">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>وضع الاختبار:</strong> هذه صفحة دفع تجريبية لاختبار النظام بدون مفاتيح Stripe حقيقية
                </div>

                <!-- Payment Details -->
                <div class="text-center mb-4">
                    <h3><i class="bi bi-credit-card me-2"></i>تأكيد الدفع</h3>
                </div>

                <div class="amount-display">
                    <h4>المبلغ المطلوب</h4>
                    <h2 class="text-success"><span id="amount">{{ $amount ?? 0 }}</span> ريال</h2>
                    <p class="text-muted">{{ $coupon_name ?? 'كوبون اختبار' }}</p>
                </div>

                <!-- Mock Payment Form -->
                <form id="mockPaymentForm">
                    @csrf
                    <input type="hidden" id="paymentIntentId" value="{{ $payment_intent_id ?? '' }}">
                    <input type="hidden" id="couponId" value="{{ $coupon_id ?? '' }}">

                    <div class="mb-3">
                        <label class="form-label">رقم البطاقة (اختبار)</label>
                        <input type="text" class="form-control" value="4242 4242 4242 4242" readonly>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">تاريخ الانتهاء</label>
                            <input type="text" class="form-control" value="12/25" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label">CVV</label>
                            <input type="text" class="form-control" value="123" readonly>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-payment btn-lg">
                            <span class="normal-text">
                                <i class="bi bi-check-circle me-2"></i>تأكيد الدفع
                            </span>
                            <span class="loading">
                                <i class="bi bi-hourglass-split me-2"></i>جاري المعالجة...
                            </span>
                        </button>
                        <a href="{{ url('/coupons') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>العودة للكوبونات
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('mockPaymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const button = this.querySelector('button[type="submit"]');
            const normalText = button.querySelector('.normal-text');
            const loadingText = button.querySelector('.loading');

            // Show loading state
            button.disabled = true;
            normalText.style.display = 'none';
            loadingText.style.display = 'inline';

            // Simulate payment processing
            setTimeout(() => {
                const paymentIntentId = document.getElementById('paymentIntentId').value;
                const couponId = document.getElementById('couponId').value;

                // Call the payment success endpoint
                fetch('/stripe/payment-success', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        payment_intent_id: paymentIntentId,
                        coupon_id: couponId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert('🎉 ' + data.message);
                        // Redirect to coupons page
                        window.location.href = '/coupons';
                    } else {
                        alert('❌ خطأ: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ حدث خطأ أثناء معالجة الدفع');
                })
                .finally(() => {
                    // Reset button state
                    button.disabled = false;
                    normalText.style.display = 'inline';
                    loadingText.style.display = 'none';
                });
            }, 2000); // Simulate 2 second processing time
        });
    </script>
</body>
</html>
