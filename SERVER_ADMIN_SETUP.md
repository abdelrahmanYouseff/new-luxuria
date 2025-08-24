# إعداد المستخدمين Admin على الخادم

## المشكلة
عمود `phone` غير موجود في جدول `users` على الخادم، مما يسبب خطأ عند تشغيل الـ seeder.

## الحل

### الخطوة 1: تشغيل Migration لإضافة عمود Phone
```bash
# في الخادم
php artisan migrate
```

### الخطوة 2: تشغيل Seeder المبسط
```bash
# في الخادم
php artisan db:seed --class=SimpleAdminUserSeeder
```

### الخطوة 3: إنشاء مستخدم Admin إضافي (اختياري)
```bash
# في الخادم
php artisan admin:simple newadmin@luxuria.com newpass123 "New Admin"
```

## المستخدمين المتاحين بعد التشغيل

| Email | Password | Name |
|-------|----------|------|
| admin@luxuria.com | admin123 | Super Admin |
| admin@rentluxuria.com | password123 | Rent Luxuria Admin |
| admin@luxuria-uae.com | admin123456 | Luxuria UAE Admin |
| test@admin.com | test123 | Test Admin User |
| system@luxuria.com | system123 | System Administrator |

## اختبار الـ Login

1. اذهب إلى: https://rentluxuria.com/login
2. استخدم أي من البيانات أعلاه
3. يجب أن يتم توجيهك مباشرة للـ dashboard

## إذا فشل Migration

إذا فشل migration عمود phone، يمكنك:

### الخيار 1: إنشاء Migration جديد
```bash
# في الخادم
php artisan make:migration add_phone_to_users_table_fix
```

ثم أضف في الملف الجديد:
```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'phone')) {
            $table->string('phone')->nullable()->after('email');
        }
    });
}
```

### الخيار 2: استخدام Seeder المبسط فقط
```bash
# في الخادم
php artisan db:seed --class=SimpleAdminUserSeeder
```

## ملاحظات مهمة

1. **الـ seeder المبسط** لا يحتوي على عمود `phone`
2. **جميع المستخدمين Admin** سيتم توجيههم للـ dashboard
3. **يمكن إضافة عمود phone لاحقاً** إذا كان مطلوباً
4. **البيانات الأساسية** (اسم، إيميل، كلمة مرور، رول) موجودة
