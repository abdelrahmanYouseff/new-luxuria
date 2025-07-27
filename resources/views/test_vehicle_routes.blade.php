<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Routes Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .route { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .admin { background-color: #e8f5e8; border-color: #4caf50; }
        .public { background-color: #fff3e0; border-color: #ff9800; }
        .btn { display: inline-block; padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; color: white; }
        .btn-admin { background-color: #4caf50; }
        .btn-public { background-color: #ff9800; }
        .info { background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🚗 اختبار راوتات السيارات</h1>

    <div class="info">
        <h3>📋 ملاحظة مهمة:</h3>
        <p><strong>المشكلة:</strong> السيارات المخفية تختفي من الجدول</p>
        <p><strong>الحل:</strong> استخدم الراوت الصحيح حسب نوع المستخدم</p>
    </div>

    <div class="route admin">
        <h2>👨‍💼 صفحة المدير (جميع السيارات)</h2>
        <p><strong>الراوت:</strong> <code>/vehicles-auth</code></p>
        <p><strong>الميزات:</strong></p>
        <ul>
            <li>✅ يعرض جميع السيارات (المرئية والمخفية)</li>
            <li>✅ السيارات المخفية لها تأثير بصري مميز</li>
            <li>✅ يمكن إخفاء/إظهار السيارات</li>
            <li>✅ زر "Show Hidden" للتحكم في العرض</li>
        </ul>
        <a href="/vehicles-auth" class="btn btn-admin">🚀 اذهب لصفحة المدير</a>
    </div>

    <div class="route public">
        <h2>🌐 الموقع العام (السيارات المرئية فقط)</h2>
        <p><strong>الراوت:</strong> <code>/vehicles</code></p>
        <p><strong>الميزات:</strong></p>
        <ul>
            <li>✅ يعرض فقط السيارات المرئية</li>
            <li>✅ السيارات المخفية لا تظهر أبداً</li>
            <li>✅ لا توجد خيارات إدارية</li>
            <li>✅ للزوار العاديين</li>
        </ul>
        <a href="/vehicles" class="btn btn-public">🌐 اذهب للموقع العام</a>
    </div>

    <div class="info">
        <h3>🔧 كيفية الاختبار:</h3>
        <ol>
            <li>اذهب إلى <strong>صفحة المدير</strong> (<code>/vehicles-auth</code>)</li>
            <li>اخفي سيارة من القائمة المنسدلة</li>
            <li>ستلاحظ أن السيارة <strong>تبقى في الجدول</strong> مع تأثير أحمر</li>
            <li>اذهب إلى <strong>الموقع العام</strong> (<code>/vehicles</code>)</li>
            <li>ستلاحظ أن السيارة <strong>لا تظهر</strong> في الموقع العام</li>
        </ol>
    </div>

    <div class="info">
        <h3>🎯 النتيجة المتوقعة:</h3>
        <p><strong>في صفحة المدير:</strong> جميع السيارات + تأثيرات بصرية للسيارات المخفية</p>
        <p><strong>في الموقع العام:</strong> فقط السيارات المرئية</p>
    </div>

    <script>
        // إضافة معلومات إضافية
        console.log('🚗 Vehicle Routes Test Page');
        console.log('📋 Admin Route: /vehicles-auth (all vehicles)');
        console.log('🌐 Public Route: /vehicles (visible vehicles only)');
    </script>
</body>
</html>
