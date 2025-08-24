# حل مشكلة الـ Redirect على الخادم

## المشكلة
الـ login يعمل على الـ local ويتم التوجيه للـ dashboard، ولكن على الـ server لا يتم التوجيه.

## الحلول المطلوبة

### 1. اختبار الـ Redirect على الخادم
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan test:server-redirect admin@rentluxuria.com password123
```

### 2. مسح الـ Caches
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan optimize:clear
```

### 3. التحقق من الـ Routes
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan route:list | grep dashboard
```

### 4. التحقق من الـ Logs
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ tail -f storage/logs/laravel.log
```

### 5. إعادة تشغيل الخدمات
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ sudo systemctl restart php8.3-fpm
forge@lively-mountain:~/rentluxuria.com$ sudo systemctl restart nginx
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

### الخيار 3: التحقق من الـ Middleware
```bash
# في الخادم
forge@lively-mountain:~/rentluxuria.com$ php artisan tinker --execute="echo 'Middleware: ' . json_encode(app('router')->getMiddleware(), JSON_PRETTY_PRINT) . PHP_EOL;"
```

## الأوامر الكاملة للتنفيذ

```bash
# في الخادم - تنفيذ جميع الأوامر
forge@lively-mountain:~/rentluxuria.com$ php artisan optimize:clear
forge@lively-mountain:~/rentluxuria.com$ php artisan test:server-redirect admin@rentluxuria.com password123
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
5. **جرب تغيير Session Driver**
