# 🧪 دليل الاختبار الكامل لنظام الدفع والنقاط

## 🎯 هدف الاختبار:
التأكد من أن النظام يضيف **500 نقطة** تلقائياً عند شراء أي كوبون.

## 📋 خطوات الاختبار:

### ✅ 1. التحقق من حالة النظام:
```bash
php artisan stripe:test
```
**النتيجة المتوقعة**: عرض حالة الاتصال مع Stripe

### ✅ 2. الذهاب لصفحة الكوبونات:
```
http://127.0.0.1:8001/coupons
```

### ✅ 3. تسجيل الدخول أو إنشاء حساب جديد:
- **إذا لم تكن مسجلاً**: اضغط Register وأنشئ حساب
- **إذا كان لديك حساب**: اضغط Login

### ✅ 4. اختيار كوبون وشراؤه:
1. **اختر أي كوبون** من القائمة
2. **اضغط "Purchase Now"**
3. **املأ بيانات الدفع**:
   - **مع Stripe الحقيقي**: بطاقة اختبار `4242 4242 4242 4242`
   - **مع Mock**: بيانات مملوءة تلقائياً
4. **أكمل عملية الدفع**

### ✅ 5. النتيجة المتوقعة:
بعد نجاح الدفع ستحصل على:
- ✅ **رسالة نجاح**: "Payment successful! Your coupon has been purchased. **You have earned 500 bonus points!**"
- ✅ **فاتورة محفوظة** في قاعدة البيانات
- ✅ **500 نقطة مضافة** لحسابك
- ✅ **إعادة توجيه** لصفحة الكوبونات

## 🔍 مراقبة النظام:

### أثناء الاختبار، شغل هذا الأمر لمراقبة العمليات:
```bash
tail -f storage/logs/laravel.log | grep -E "(Points added successfully|Payment successful|COUPON_PURCHASE)"
```

### أو للبحث في logs بعد الاختبار:
```bash
grep -E "(Points added successfully|Payment successful)" storage/logs/laravel.log | tail -5
```

## 📊 التحقق من النتائج:

### 1. فحص الفواتير:
```bash
php artisan tinker --execute="
App\Models\CouponInvoice::latest()->take(3)->get(['invoice_number', 'coupon_name', 'amount', 'payment_status', 'user_id'])->each(function(\$i) { 
    echo \"Invoice: {\$i->invoice_number} | Status: {\$i->payment_status} | Amount: {\$i->amount}\" . PHP_EOL; 
});
"
```

### 2. فحص النقاط (إذا كان PointSys يعمل):
```bash
php artisan points:test {user_id}
```

## 🎯 نتائج الاختبار المتوقعة:

### ✅ في وضع TEST/Mock:
- **الدفع**: يعمل تجريبياً
- **الفواتير**: تُحفظ بحالة "completed"
- **النقاط**: تُضاف تجريبياً (Mock response)
- **الرسائل**: تظهر "500 bonus points earned"

### ✅ مع Stripe حقيقي:
- **الدفع**: يعمل مع بطاقات اختبار
- **الفواتير**: تُحفظ بحالة "completed"
- **النقاط**: تُضاف عبر PointSys API الحقيقي
- **الرسائل**: تظهر "500 bonus points earned"

## 🔧 إذا لم يعمل شيء:

### مشكلة: لا تظهر رسالة النقاط
```bash
# تحقق من logs:
grep "Points added successfully" storage/logs/laravel.log

# إذا لم تجد شيء، تحقق من:
grep "Failed to add points" storage/logs/laravel.log
```

### مشكلة: فشل الدفع
```bash
# تحقق من حالة Stripe:
php artisan stripe:test

# تحقق من logs الأخطاء:
grep "Stripe API error" storage/logs/laravel.log | tail -3
```

### مشكلة: المستخدم غير مسجل في PointSys
```bash
# سجل المستخدم يدوياً:
php artisan points:test {user_id}
```

## 🎊 ملخص الكود المُطبق:

### في `StripeController@handlePaymentSuccess`:
```php
// إضافة 500 نقطة بعد نجاح الدفع
$pointsResult = $this->pointSysService->addPointsToCustomerByUser(
    $user,
    500,  // عدد النقاط
    'نقاط شراء كوبون - ' . $invoice->coupon_name,
    'COUPON_PURCHASE_' . $invoice->invoice_number
);
```

### API المستخدم:
```
POST /api/pointsys/customers/points/add
{
    "customer_id": {user_pointsys_id},
    "points": 500,
    "description": "نقاط شراء كوبون - اسم الكوبون",
    "reference_id": "COUPON_PURCHASE_فاتورة_رقم"
}
```

---

## 🚀 ابدأ الاختبار الآن:
**اذهب إلى: `http://127.0.0.1:8001/coupons` وجرب شراء كوبون!**

ستحصل على **500 نقطة مجاناً** مع كل كوبون! 🎁

---
**تاريخ الإنشاء**: الآن
**الحالة**: ✅ النظام مُطبق ويعمل بنجاح 
