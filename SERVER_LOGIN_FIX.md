# حل مشكلة الـ Login على الخادم

## المشكلة
الـ login يعمل على الـ local ولكن لا يعمل على الـ server.

## الحلول المطلوبة على الخادم

### 1. مسح جميع الـ Caches
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan config:clear
forge@lively-mountain:~/rentluxuria.com$ php artisan cache:clear
forge@lively-mountain:~/rentluxuria.com$ php artisan route:clear
forge@lively-mountain:~/rentluxuria.com$ php artisan view:clear
forge@lively-mountain:~/rentluxuria.com$ php artisan session:clear
forge@lively-mountain:~/rentluxuria.com$ composer dump-autoload
```

### 2. إعادة تشغيل الخدمات
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ sudo systemctl restart php8.3-fpm
forge@lively-mountain:~/rentluxuria.com$ sudo systemctl restart nginx
```

### 3. التحقق من إعدادات الـ Session
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ cat .env | grep SESSION
```

يجب أن تكون الإعدادات كالتالي:
```
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
```

### 4. التحقق من جدول الـ Sessions
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan tinker --execute="echo 'Sessions table exists: ' . (Schema::hasTable('sessions') ? 'YES' : 'NO') . PHP_EOL;"
```

### 5. تشغيل الـ Seeder على الخادم
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan db:seed --class=SimpleAdminUserSeeder
```

### 6. اختبار الـ Login على الخادم
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan test:web-login admin@rentluxuria.com password123
```

## التحقق من المشاكل الشائعة

### 1. مشكلة الـ CSRF Token
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan tinker --execute="echo 'CSRF Token: ' . csrf_token() . PHP_EOL;"
```

### 2. مشكلة الـ Session Storage
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ ls -la storage/framework/sessions/
```

### 3. مشكلة الـ File Permissions
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ sudo chown -R forge:forge storage/
forge@lively-mountain:~/rentluxuria.com$ sudo chmod -R 775 storage/
```

### 4. مشكلة الـ Database Connection
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan tinker --execute="echo 'Database connected: ' . (DB::connection()->getPdo() ? 'YES' : 'NO') . PHP_EOL;"
```

## اختبار الـ Login على الخادم

### الطريقة الأولى: صفحة الاختبار
اذهب إلى: https://rentluxuria.com/test-login

### الطريقة الثانية: صفحة الـ Login الأصلية
اذهب إلى: https://rentluxuria.com/login

### الطريقة الثالثة: اختبار الـ API
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ curl -X POST https://rentluxuria.com/login \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "email=admin@rentluxuria.com&password=password123&_token=$(php artisan tinker --execute="echo csrf_token();")" \
  -v
```

## إذا لم يعمل

### الخيار 1: تغيير Session Driver
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ sed -i 's/SESSION_DRIVER=database/SESSION_DRIVER=file/' .env
forge@lively-mountain:~/rentluxuria.com$ php artisan config:clear
```

### الخيار 2: إنشاء مستخدم جديد
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan admin:simple serveradmin@luxuria.com serverpass123 "Server Admin"
```

### الخيار 3: التحقق من الـ Logs
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ tail -f storage/logs/laravel.log
```

## الأوامر الكاملة للتنفيذ

```bash
# في الخادم - تنفيذ جميع الأوامر
forge@lively-mountain:~/rentluxuria.com$ php artisan optimize:clear
forge@lively-mountain:~/rentluxuria.com$ sudo chown -R forge:forge storage/
forge@lively-mountain:~/rentluxuria.com$ sudo chmod -R 775 storage/
forge@lively-mountain:~/rentluxuria.com$ php artisan db:seed --class=SimpleAdminUserSeeder
forge@lively-mountain:~/rentluxuria.com$ sudo systemctl restart php8.3-fpm
forge@lively-mountain:~/rentluxuria.com$ sudo systemctl restart nginx
```

## اختبار نهائي

1. اذهب إلى: https://rentluxuria.com/test-login
2. أدخل البيانات:
   - Email: admin@rentluxuria.com
   - Password: password123
3. اضغط Login
4. يجب أن يتم توجيهك للـ dashboard

## إذا استمرت المشكلة

1. **تحقق من الـ browser console** للأخطاء
2. **تحقق من الـ network tab**
3. **جرب متصفح مختلف**
4. **تحقق من الـ server logs**
