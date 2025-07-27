# PointSys Integration Guide

## ✅ تم إعداد التكامل بنجاح!

تم إعداد نظام PointSys API بالكامل في التطبيق. إليك ملخص ما تم تنفيذه:

### 🔧 الملفات المحدثة:

1. **`app/Services/PointSysService.php`** - Service للتعامل مع PointSys API
2. **`app/Http/Controllers/PointSysController.php`** - Controller للـ API endpoints
3. **`app/Http/Controllers/UserPointsController.php`** - Controller لنقاط المستخدم
4. **`app/Http/Controllers/Auth/RegisteredUserController.php`** - تم تحديثه لاستدعاء PointSys API
5. **`app/Models/User.php`** - تم إضافة حقل `pointsys_customer_id`
6. **`resources/js/pages/auth/Register.vue`** - تم إضافة حقل الهاتف
7. **Migration** - تم إضافة حقل `pointsys_customer_id` إلى جدول المستخدمين

### 🚀 الميزات المضافة:

#### 1. تسجيل المستخدم التلقائي في PointSys
عندما يسجل مستخدم جديد في التطبيق:
- يتم إنشاء المستخدم في قاعدة البيانات المحلية
- يتم استدعاء PointSys API لتسجيل العميل
- يتم حفظ `customer_id` من PointSys في قاعدة البيانات
- يتم تسجيل العملية في الـ logs

#### 2. إدارة نقاط المستخدم
- `GET /my-points` - عرض نقاط المستخدم الحالي
- `POST /add-points` - إضافة نقاط للمستخدم
- `GET /rewards` - عرض المكافآت المتاحة
- `POST /redeem-reward` - استبدال مكافأة

#### 3. API Endpoints للـ PointSys
- `POST /api/pointsys/customers/register` - تسجيل عميل جديد
- `GET /api/pointsys/customers/{id}/balance` - استعلام رصيد النقاط
- `POST /api/pointsys/customers/points/add` - إضافة نقاط
- `GET /api/pointsys/rewards` - عرض المكافآت
- `POST /api/pointsys/rewards/redeem` - استبدال مكافأة

### 📝 كيفية الاستخدام:

#### 1. تسجيل مستخدم جديد
```bash
# من خلال الواجهة الأمامية
# اذهب إلى /register وأدخل البيانات المطلوبة
# سيتم استدعاء PointSys API تلقائياً
```

#### 2. اختبار الـ API
```bash
# اختبار تسجيل عميل جديد
curl -X POST "http://127.0.0.1:8001/api/pointsys/customers/register" \
  -H "Content-Type: application/json" \
  -d '{"name": "أحمد محمد", "email": "ahmed@example.com", "phone": "0501234567"}'

# اختبار استعلام النقاط
curl -X GET "http://127.0.0.1:8001/api/pointsys/customers/1/balance"

# اختبار إضافة نقاط
curl -X POST "http://127.0.0.1:8001/api/pointsys/customers/points/add" \
  -H "Content-Type: application/json" \
  -d '{"customer_id": 1, "points": 100, "description": "Car rental bonus", "reference_id": "ORDER_123"}'
```

#### 3. استخدام Service في الكود
```php
use App\Services\PointSysService;

class SomeController extends Controller
{
    private PointSysService $pointSysService;

    public function __construct(PointSysService $pointSysService)
    {
        $this->pointSysService = $pointSysService;
    }

    public function someMethod()
    {
        // تسجيل عميل جديد
        $customer = $this->pointSysService->registerCustomer([
            'name' => 'أحمد محمد',
            'email' => 'ahmed@example.com',
            'phone' => '0501234567'
        ]);

        // إضافة نقاط
        $result = $this->pointSysService->addPointsToCustomer(1, 100, 'Car rental bonus', 'ORDER_123');
    }
}
```

### 🔍 مراقبة الأخطاء:

جميع الأخطاء يتم تسجيلها في `storage/logs/laravel.log`:

```bash
# مراقبة الـ logs
tail -f storage/logs/laravel.log
```

### ⚠️ ملاحظات مهمة:

1. **API Key**: تأكد من أن الـ API key صحيح في ملف `.env`
2. **الاتصال بالإنترنت**: تأكد من أن الخادم متصل بالإنترنت للوصول إلى PointSys API
3. **الأخطاء المتوقعة**: إذا كان PointSys API غير متاح، سيتم تسجيل الأخطاء في الـ logs ولكن التطبيق سيستمر في العمل
4. **التسجيل التلقائي**: حتى لو فشل تسجيل المستخدم في PointSys، سيتم إنشاء المستخدم في قاعدة البيانات المحلية

### 🧪 اختبار النظام:

```bash
# 1. تشغيل الخادم
php artisan serve --host=127.0.0.1 --port=8001

# 2. اختبار تسجيل مستخدم جديد
# اذهب إلى http://127.0.0.1:8001/register

# 3. مراقبة الـ logs
tail -f storage/logs/laravel.log

# 4. اختبار الـ API endpoints
curl -X GET "http://127.0.0.1:8001/api/pointsys/rewards"
```

### 📊 قاعدة البيانات:

تم إضافة حقل `pointsys_customer_id` إلى جدول المستخدمين:
- `pointsys_customer_id` (BIGINT, NULLABLE) - معرف العميل في PointSys
- تم إنشاء index على الحقل لتحسين الأداء

### 🔐 الأمان:

- جميع الـ API endpoints محمية بـ API key
- يتم التحقق من صحة البيانات قبل إرسالها
- جميع العمليات يتم تسجيلها في الـ logs
- الأخطاء يتم التعامل معها بشكل آمن

---

## 🎉 النظام جاهز للاستخدام!

يمكنك الآن:
1. تسجيل مستخدمين جدد (سيتم تسجيلهم تلقائياً في PointSys)
2. إدارة نقاط المستخدمين
3. عرض واستبدال المكافآت
4. مراقبة جميع العمليات من خلال الـ logs 
