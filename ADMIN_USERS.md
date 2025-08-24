# مستخدمين Admin - Luxuria UAE

## المستخدمين المتاحين حالياً

### 1. Super Admin
- **Email**: admin@luxuria.com
- **Password**: admin123
- **Name**: Super Admin
- **Location**: Dubai, UAE

### 2. Rent Luxuria Admin
- **Email**: admin@rentluxuria.com
- **Password**: password123
- **Name**: Rent Luxuria Admin
- **Location**: Abu Dhabi, UAE

### 3. Luxuria UAE Admin
- **Email**: admin@luxuria-uae.com
- **Password**: admin123456
- **Name**: Luxuria UAE Admin
- **Location**: Sharjah, UAE

### 4. Test Admin User
- **Email**: test@admin.com
- **Password**: test123
- **Name**: Test Admin User
- **Location**: Ajman, UAE

### 5. System Administrator
- **Email**: system@luxuria.com
- **Password**: system123
- **Name**: System Administrator
- **Location**: Ras Al Khaimah, UAE

### 6. Test Admin (New)
- **Email**: testadmin@luxuria.com
- **Password**: testpass123
- **Name**: Test Admin
- **Location**: Dubai, UAE

## كيفية إنشاء مستخدم Admin جديد

### الطريقة الأولى: استخدام Seeder
```bash
php artisan db:seed --class=AdminUserSeeder
```

### الطريقة الثانية: استخدام Command السريع
```bash
php artisan admin:create email@example.com password123 "Admin Name"
```

### الطريقة الثالثة: استخدام Command التفصيلي
```bash
php artisan user:create-admin email@example.com password123 "Admin Name"
```

## ملاحظات مهمة

1. **جميع المستخدمين Admin يتم توجيههم مباشرة للـ Dashboard** بعد الـ login
2. **المستخدمين العاديين** يتم توجيههم للصفحة الرئيسية
3. **جميع المستخدمين Admin** لديهم صلاحيات كاملة على النظام
4. **يمكن تعديل البيانات** من خلال قاعدة البيانات مباشرة

## اختبار الـ Login

1. اذهب إلى: http://127.0.0.1:8000/login
2. استخدم أي من البيانات أعلاه
3. يجب أن يتم توجيهك مباشرة للـ dashboard

## إضافة مستخدم Admin جديد

لإضافة مستخدم admin جديد، أضف البيانات في ملف `database/seeders/AdminUserSeeder.php`:

```php
[
    'name' => 'New Admin Name',
    'email' => 'newadmin@example.com',
    'password' => 'newpassword123',
    'role' => 'admin',
    'phone' => '+971500000000',
    'emirate' => 'Dubai',
    'address' => 'Dubai, UAE',
],
```

ثم قم بتشغيل:
```bash
php artisan db:seed --class=AdminUserSeeder
```
