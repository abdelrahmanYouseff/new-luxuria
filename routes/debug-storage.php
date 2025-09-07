<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

// Route لتشخيص مشاكل التخزين
Route::get('/debug-storage', function () {
    $debug = [];

    // 1. فحص symbolic link
    $storageLink = public_path('storage');
    $debug['symbolic_link_exists'] = is_link($storageLink);
    $debug['symbolic_link_target'] = $debug['symbolic_link_exists'] ? readlink($storageLink) : null;

    // 2. فحص مجلد التخزين
    $storagePath = storage_path('app/public');
    $debug['storage_path_exists'] = is_dir($storagePath);
    $debug['storage_path_writable'] = is_writable($storagePath);

    // 3. فحص مجلد المركبات
    $vehiclesPath = storage_path('app/public/vehicles');
    $debug['vehicles_path_exists'] = is_dir($vehiclesPath);
    $debug['vehicles_path_writable'] = is_writable($vehiclesPath);

    // 4. عدد الصور الموجودة
    if ($debug['vehicles_path_exists']) {
        $files = File::files($vehiclesPath);
        $debug['vehicles_count'] = count($files);
        $debug['sample_files'] = array_slice(array_map(function($file) {
            return basename($file);
        }, $files), 0, 5);
    }

    // 5. فحص إعدادات التخزين
    $debug['filesystem_disk'] = config('filesystems.default');
    $debug['public_disk_url'] = config('filesystems.disks.public.url');
    $debug['app_url'] = config('app.url');

    // 6. اختبار إنشاء ملف
    try {
        $testFile = 'test_' . time() . '.txt';
        $testPath = storage_path('app/public/' . $testFile);
        file_put_contents($testPath, 'test');
        $debug['can_create_file'] = file_exists($testPath);
        if ($debug['can_create_file']) {
            unlink($testPath);
        }
    } catch (Exception $e) {
        $debug['can_create_file'] = false;
        $debug['create_file_error'] = $e->getMessage();
    }

    // 7. اختبار الوصول للصورة
    if ($debug['vehicles_count'] > 0) {
        $sampleFile = $debug['sample_files'][0] ?? null;
        if ($sampleFile) {
            $debug['sample_file_path'] = storage_path('app/public/vehicles/' . $sampleFile);
            $debug['sample_file_exists'] = file_exists($debug['sample_file_path']);
            $debug['sample_file_url'] = asset('storage/vehicles/' . $sampleFile);
            $debug['sample_storage_url'] = Storage::disk('public')->url('vehicles/' . $sampleFile);
        }
    }

    return response()->json($debug, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});

// Route لاختبار رفع صورة
Route::post('/test-upload', function (Illuminate\Http\Request $request) {
    try {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = $request->file('image')->store('test-uploads', 'public');

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'path' => $imagePath,
            'url' => Storage::disk('public')->url($imagePath),
            'asset_url' => asset('storage/' . $imagePath)
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage()
        ], 500);
    }
});

// Route لإصلاح symbolic link
Route::get('/fix-storage-link', function () {
    try {
        $publicStorage = public_path('storage');
        $storageAppPublic = storage_path('app/public');

        // حذف الرابط الموجود إذا كان موجود
        if (is_link($publicStorage)) {
            unlink($publicStorage);
        }

        // إنشاء رابط جديد
        symlink($storageAppPublic, $publicStorage);

        return response()->json([
            'success' => true,
            'message' => 'Storage link created successfully',
            'link_path' => $publicStorage,
            'target_path' => $storageAppPublic
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create storage link: ' . $e->getMessage()
        ], 500);
    }
});
