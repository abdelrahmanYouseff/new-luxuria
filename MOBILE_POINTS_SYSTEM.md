# نظام النقاط في التطبيق المحمول

## نظرة عامة
تم إضافة نظام النقاط للتطبيق المحمول بحيث يحصل العميل على **5 نقاط لكل يوم إيجار** بعد إتمام عملية الدفع بنجاح.

## كيفية عمل النظام

### 1. حساب النقاط
- **5 نقاط لكل يوم إيجار**
- مثال: إيجار سيارة لمدة 4 أيام = 20 نقطة
- مثال: إيجار سيارة لمدة 7 أيام = 35 نقطة

### 2. توقيت إضافة النقاط
- يتم حساب النقاط عند إنشاء الحجز
- يتم إضافة النقاط للعميل **بعد إتمام عملية الدفع بنجاح**
- النقاط تُضاف إلى نظام PointSys تلقائياً

### 3. التحديثات في MobileReservationController

#### أ. إنشاء الحجز (`createReservation`)
```php
// حساب النقاط (5 نقاط لكل يوم)
$pointsToEarn = $totalDays * 5;

// إنشاء الحجز مع النقاط
$localBooking = Booking::create([
    // ... بيانات الحجز
    'points_earned' => $pointsToEarn,
    'points_added_to_customer' => false, // سيتم تحديثها بعد الدفع
]);
```

#### ب. تأكيد الدفع (`confirmPayment`)
```php
// إضافة النقاط للعميل بعد نجاح الدفع
$pointsResult = $this->bookingPointsService->addPointsToCustomer($booking);

// إنشاء فاتورة الحجز
$invoiceResult = $this->bookingInvoiceService->createInvoice($booking);
```

### 4. API Endpoints الجديدة

#### أ. إحصائيات النقاط
```
GET /api/mobile/points/booking-stats
```
**الاستجابة:**
```json
{
    "success": true,
    "message": "تم جلب إحصائيات النقاط بنجاح",
    "data": {
        "total_bookings": 5,
        "total_points_earned": 120,
        "total_days_rented": 24,
        "points_per_day": 5
    }
}
```

#### ب. تاريخ النقاط
```
GET /api/mobile/points/booking-history
```
**الاستجابة:**
```json
{
    "success": true,
    "message": "تم جلب تاريخ النقاط بنجاح",
    "data": {
        "bookings": [
            {
                "booking_id": 51,
                "vehicle": {
                    "make": "BMW",
                    "model": "X5",
                    "year": 2023,
                    "plate_number": "ABC123"
                },
                "dates": {
                    "start_date": "2025-08-25",
                    "end_date": "2025-08-27",
                    "total_days": 2
                },
                "points": {
                    "points_earned": 10,
                    "points_per_day": 5
                },
                "total_amount": 400,
                "created_at": "2025-08-25T10:30:00.000000Z"
            }
        ],
        "total_count": 1
    }
}
```

### 5. معلومات النقاط في الحجوزات

#### أ. عند إنشاء الحجز
```json
{
    "points": {
        "points_earned": 20,
        "points_added_to_customer": false,
        "points_message": "سيتم إضافة 20 نقطة بعد إتمام عملية الدفع"
    }
}
```

#### ب. عند جلب الحجوزات
```json
{
    "points": {
        "points_earned": 20,
        "points_added_to_customer": true,
        "points_status": "تم إضافة النقاط"
    }
}
```

#### ج. عند تأكيد الدفع
```json
{
    "points_added": {
        "success": true,
        "message": "تم إضافة 20 نقطة بنجاح",
        "points_earned": 20,
        "points_added_to_customer": true,
        "total_points": 140
    }
}
```

### 6. اختبار النظام

#### أمر الاختبار
```bash
php artisan test:mobile-points --days=4
```

#### مثال على النتائج
```
🧪 Testing Mobile Points System...
👤 Testing with user: Abdelrahman (abdelrahmanyouseff@gmail.com)
📅 Created test booking for 4 days
📊 Booking Details:
   - ID: 54
   - Total Days: 4
   - Points Earned: 20
   - Points Added: No
🧮 Points Calculation:
   - Expected: 4 days × 5 points = 20 points
   - Calculated: 20 points
   - Match: ✅
```

### 7. الخدمات المستخدمة

#### أ. BookingPointsService
- `calculatePointsForBooking()`: حساب النقاط للحجز
- `addPointsToCustomer()`: إضافة النقاط للعميل
- `getCustomerPointsStats()`: إحصائيات النقاط
- `getBookingHistory()`: تاريخ النقاط

#### ب. BookingInvoiceService
- `createInvoice()`: إنشاء فاتورة الحجز

### 8. التكامل مع PointSys
- النقاط تُضاف تلقائياً إلى نظام PointSys
- يتم إرسال وصف تفصيلي: "نقاط إيجار سيارة - BMW X5 - 4 يوم"
- يتم إنشاء معرف مرجعي: "BOOKING_123"

### 9. الأمان والتحقق
- التحقق من عدم إضافة النقاط مرتين
- التحقق من وجود العميل في نظام PointSys
- تسجيل جميع العمليات في السجلات

## ملاحظات مهمة
1. النقاط تُحسب فقط للحجوزات المؤكدة والمدفوعة
2. كل يوم إيجار = 5 نقاط ثابتة
3. النظام متكامل مع PointSys تلقائياً
4. يتم إنشاء فواتير للحجوزات المؤكدة
5. جميع العمليات مسجلة ومتابعة
