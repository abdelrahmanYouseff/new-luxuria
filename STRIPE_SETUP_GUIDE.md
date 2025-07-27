# 🎯 دليل إعداد Stripe الحقيقي

## 📋 الخطوات المطلوبة:

### ✅ 1. الحصول على مفاتيح Stripe
من [Stripe Dashboard](https://dashboard.stripe.com/):

#### للاختبار (Test Mode):
```
STRIPE_PUBLIC_KEY=pk_test_51xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_test_51xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

#### للإنتاج (Live Mode):
```
STRIPE_PUBLIC_KEY=pk_live_51xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_live_51xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### ✅ 2. إضافة المفاتيح في ملف `.env`

**استبدل القيم في `.env`:**
```bash
# Before (Mock Mode):
STRIPE_PUBLIC_KEY=pk_test_your_public_key_here
STRIPE_SECRET_KEY=sk_test_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here

# After (Real Stripe):
STRIPE_PUBLIC_KEY=pk_test_51xxxxxxxxxx  # Your actual public key
STRIPE_SECRET_KEY=sk_test_51xxxxxxxxxx  # Your actual secret key
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxx  # Your actual webhook secret
```

### ✅ 3. إعداد Webhooks (اختياري لكن مُوصى به)

في Stripe Dashboard → Webhooks → Add endpoint:
- **URL**: `https://yourdomain.com/stripe/webhook`
- **Events**: `payment_intent.succeeded`, `payment_intent.payment_failed`

### ✅ 4. تأكيد الإعدادات

بعد إضافة المفاتيح، شغّل:
```bash
php artisan config:clear
php artisan config:cache
```

## 🔍 كيفية التحقق من أن النظام يستخدم Stripe الحقيقي:

### 1. افحص صفحة الكوبونات:
اذهب إلى: `http://127.0.0.1:8001/coupons`

### 2. إذا كانت المفاتيح صحيحة:
- ❌ **لن تظهر** رسالة "وضع الاختبار"
- ✅ **ستظهر** أزرار "Purchase Now" عادية
- ✅ عند الضغط سيوجهك لـ **Stripe Checkout الحقيقي**

### 3. إذا كانت المفاتيح خاطئة:
- ⚠️ **ستظهر** رسالة "وضع الاختبار"
- 🧪 **سيستخدم** النظام التجريبي

## 🧪 اختبار النظام مع Stripe الحقيقي:

### بطاقات الاختبار (Test Mode):
```
رقم البطاقة: 4242 4242 4242 4242
انتهاء: أي تاريخ مستقبلي (مثل 12/25)
CVV: أي 3 أرقام (مثل 123)
```

### بطاقات أخرى للاختبار:
- **Visa**: 4242 4242 4242 4242
- **Mastercard**: 5555 5555 5555 4444
- **American Express**: 3782 822463 10005
- **فشل الدفع**: 4000 0000 0000 0002

## 🔧 استكشاف الأخطاء:

### مشكلة: "Invalid API Key"
```bash
# تأكد من:
1. المفتاح صحيح ولا يحتوي على مسافات
2. يبدأ بـ pk_test_ أو pk_live_
3. ملف .env محفوظ
4. شغلت php artisan config:clear
```

### مشكلة: "Payment fails"
```bash
# تحقق من:
1. المفتاح السري (secret key) صحيح
2. Account في Stripe مفعل
3. لا توجد قيود على الـ API key
```

### مشكلة: "Webhook failed"
```bash
# الـ Webhook اختياري للتشغيل الأساسي
# لكن مُوصى به للتأكد من معالجة جميع الـ events
```

## 📊 مراقبة العمليات:

### في Stripe Dashboard:
- **Payments**: عرض جميع العمليات
- **Logs**: تتبع الأخطاء
- **Webhooks**: تتبع الـ events

### في Laravel Logs:
```bash
tail -f storage/logs/laravel.log | grep -i stripe
```

## 💰 إضافة النقاط:

مع Stripe الحقيقي، النظام سيضيف **500 نقطة** تلقائياً:
- ✅ عند نجاح الدفع مباشرة
- ✅ عند استلام webhook (backup)
- ✅ مع رسالة تأكيد: "You have earned 500 bonus points!"

## 🚀 الانتقال للإنتاج:

عندما تكون جاهز للعمل الحقيقي:
1. غيّر المفاتيح من `test` إلى `live`
2. فعّل الـ webhooks للإنتاج
3. اختبر مع مبلغ صغير أولاً

---

## 📞 المساعدة:

إذا واجهت أي مشاكل:
1. تأكد من أن المفاتيح صحيحة
2. شغّل `php artisan config:clear`
3. تحقق من Laravel logs
4. تحقق من Stripe Dashboard

---
**آخر تحديث**: الآن
**الحالة**: ✅ جاهز للاختبار مع Stripe حقيقي أو Mock 
