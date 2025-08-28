# حل مشكلة Vite Manifest

## المشكلة
```
Illuminate\Foundation\ViteManifestNotFoundException
Vite manifest not found at: /path/to/public/build/manifest.json
```

## السبب
Laravel يبحث عن ملف `manifest.json` في `public/build/manifest.json` بينما Vite ينشئه في `public/build/.vite/manifest.json`.

## الحل

### 1. تحديث vite.config.ts
تأكد من أن ملف `vite.config.ts` يحتوي على الإعدادات الصحيحة:

```typescript
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts', 'resources/css/app.css'], // أضف CSS هنا
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        // ... باقي الإعدادات
    ],
    // ... باقي الإعدادات
});
```

### 2. بناء الأصول
```bash
npm run build
```

### 3. نسخ manifest.json
```bash
cp public/build/.vite/manifest.json public/build/manifest.json
```

### 4. استخدام Script المخصص (اختياري)
```bash
./build-assets.sh
```

## ملاحظات مهمة

1. **في Development**: استخدم `npm run dev` للعمل المحلي
2. **في Production**: استخدم `npm run build` ثم انسخ manifest.json
3. **تأكد من وجود الملفات**: تحقق من وجود `public/build/manifest.json` بعد البناء

## استكشاف الأخطاء

إذا استمرت المشكلة:
1. تحقق من وجود ملف `manifest.json` في `public/build/`
2. تأكد من أن جميع الملفات المطلوبة موجودة في `public/build/assets/`
3. تحقق من إعدادات Vite في `vite.config.ts`
4. تأكد من أن جميع dependencies مثبتة: `npm install`
