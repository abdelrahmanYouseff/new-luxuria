# 🚗 ميزة إظهار/إخفاء السيارات

## ✅ **تم الإضافة:**

### 1. **حقل `is_visible` في قاعدة البيانات:**
- تم إضافة حقل `is_visible` (boolean) إلى جدول `vehicles`
- القيمة الافتراضية: `true` (مرئية)
- تم إضافة index للحقل لتحسين الأداء

### 2. **Scopes جديدة في Vehicle Model:**
- `visible()` - للسيارات المرئية فقط
- `hidden()` - للسيارات المخفية فقط

### 3. **API لإدارة الرؤية:**
- `PATCH /vehicles/{vehicle}/toggle-visibility` - تبديل حالة الرؤية

### 4. **إصلاح مشكلة الراوتات:**
- تم حل تضارب الراوتات بين العام والمحمي
- الراوت العام: `/vehicles` (بدون تسجيل دخول)
- الراوت المحمي: `/vehicles-auth` (مع تسجيل دخول)

## 🔧 **الملفات المحدثة:**

### 1. **Migration:**
```php
// database/migrations/2025_07_26_120000_add_is_visible_to_vehicles_table.php
$table->boolean('is_visible')->default(true)->after('image');
$table->index('is_visible');
```

### 2. **Vehicle Model:**
```php
// app/Models/Vehicle.php
protected $fillable = [
    // ... existing fields
    'is_visible'
];

// Scopes
public function scopeVisible($query) {
    return $query->where('is_visible', true);
}

public function scopeHidden($query) {
    return $query->where('is_visible', false);
}
```

### 3. **VehicleController:**
```php
// app/Http/Controllers/VehicleController.php
// Get only visible vehicles
$vehicles = Vehicle::visible()->orderBy('daily_rate', 'desc')->get();

// Toggle visibility method
public function toggleVisibility(Vehicle $vehicle) {
    $vehicle->update(['is_visible' => !$vehicle->is_visible]);
    return response()->json([...]);
}
```

### 4. **Routes:**
```php
// routes/web.php
// Public route (no auth)
Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index.public');

// Protected route (with auth)
Route::get('/vehicles-auth', [VehicleController::class, 'index'])->name('vehicles.index');

// Toggle visibility (admin only)
Route::patch('/vehicles/{vehicle}/toggle-visibility', [VehicleController::class, 'toggleVisibility']);
```

## 🎯 **كيفية الاستخدام:**

### **للمستخدمين العاديين:**
- يروا فقط السيارات المرئية (`is_visible = true`)
- لا يمكنهم تغيير حالة الرؤية

### **للمديرين:**
- يمكنهم رؤية جميع السيارات (المرئية والمخفية)
- يمكنهم تبديل حالة الرؤية لكل سيارة

### **API Usage:**
```javascript
// تبديل حالة الرؤية
fetch(`/vehicles/${vehicleId}/toggle-visibility`, {
    method: 'PATCH',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    console.log(data.message); // "Vehicle is now visible" or "Vehicle is now hidden"
});
```

## 📋 **الخطوات المطلوبة على السيرفر:**

### 1. **رفع الملفات المحدثة:**
```bash
# ارفع هذه الملفات
database/migrations/2025_07_26_120000_add_is_visible_to_vehicles_table.php
app/Models/Vehicle.php
app/Http/Controllers/VehicleController.php
routes/web.php
```

### 2. **تشغيل Migration:**
```bash
php artisan migrate
```

### 3. **مسح الكاش:**
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan route:cache
```

## 🚀 **اختبار الميزة:**

### 1. **اختبار الراوت العام:**
```
https://wpp.rentluxuria.com/vehicles
```

### 2. **اختبار تبديل الرؤية:**
```bash
# في Tinker
php artisan tinker
>>> $vehicle = \App\Models\Vehicle::first();
>>> $vehicle->is_visible; // true
>>> $vehicle->update(['is_visible' => false]);
>>> $vehicle->fresh()->is_visible; // false
>>> exit
```

### 3. **اختبار Scopes:**
```bash
php artisan tinker
>>> \App\Models\Vehicle::visible()->count(); // السيارات المرئية
>>> \App\Models\Vehicle::hidden()->count(); // السيارات المخفية
>>> exit
```

## 📊 **معلومات إضافية:**

### **حالات السيارات:**
- **مرئية (`is_visible = true`)**: تظهر في الموقع للجميع
- **مخفية (`is_visible = false`)**: لا تظهر في الموقع

### **الترتيب:**
- السيارات مرتبة من الأغلى للأرخص
- فقط السيارات المرئية تظهر للزوار

### **الأمان:**
- فقط المستخدمين المسجلين يمكنهم تغيير حالة الرؤية
- السيارات المخفية لا تظهر في الواجهة العامة

## ✅ **تأكيد النجاح:**

بعد تطبيق الحل، يجب أن:
1. ✅ يمكن الوصول لـ `/vehicles` بدون تسجيل دخول
2. ✅ فقط السيارات المرئية تظهر للزوار
3. ✅ المديرين يمكنهم إخفاء/إظهار السيارات
4. ✅ السيارات مرتبة من الأغلى للأرخص
5. ✅ حقل `is_visible` موجود في قاعدة البيانات 
