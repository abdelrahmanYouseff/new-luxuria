# إعدادات السيرفر لحل مشكلة CSS

## المشكلة
خطأ 404 عند تحميل `/css/app.css` على السيرفر

## الحل

### 1. تأكد من أن ملف `.env` على السيرفر يحتوي على:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://rentluxuria.com
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

### 2. تأكد من أن الأصول مُجمّعة:
```bash
npm run build
```

### 3. تأكد من وجود الملفات:
- `public/build/manifest.json`
- `public/build/assets/app-I14vtt8l.css`

### 4. تأكد من أن ملف `login.blade.php` يستخدم:
```php
@vite(['resources/css/app.css'])
```

بدلاً من:
```php
<link rel="stylesheet" href="/css/app.css">
```

### 5. إذا استمرت المشكلة، جرب:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 6. تأكد من أن Vite يعمل في البيئة الإنتاجية:
```bash
php artisan tinker --execute="echo 'App Environment: ' . config('app.env');"
```

يجب أن يظهر: `production`
