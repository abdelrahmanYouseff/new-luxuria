<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FixImageStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:fix-images {--force : Force recreate symbolic link}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix image storage issues on server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Starting image storage fix...');

        // 1. Check storage directory
        $this->checkStorageDirectory();

        // 2. Fix symbolic link
        $this->fixSymbolicLink();

        // 3. Check permissions
        $this->checkPermissions();

        // 4. Test image access
        $this->testImageAccess();

        $this->info('✅ Image storage fix completed!');
    }

    private function checkStorageDirectory()
    {
        $this->info('📁 Checking storage directory...');

        $storagePath = storage_path('app/public');
        $vehiclesPath = storage_path('app/public/vehicles');

        if (!is_dir($storagePath)) {
            $this->error("❌ Storage directory not found: {$storagePath}");
            return false;
        }

        if (!is_dir($vehiclesPath)) {
            $this->warn("⚠️  Vehicles directory not found, creating...");
            File::makeDirectory($vehiclesPath, 0755, true);
        }

        $this->info("✅ Storage directory exists: {$storagePath}");
        $this->info("✅ Vehicles directory exists: {$vehiclesPath}");

        return true;
    }

    private function fixSymbolicLink()
    {
        $this->info('🔗 Fixing symbolic link...');

        $publicStorage = public_path('storage');
        $storageAppPublic = storage_path('app/public');

        // Remove existing link if force option is used
        if ($this->option('force') && is_link($publicStorage)) {
            $this->warn('🗑️  Removing existing symbolic link...');
            unlink($publicStorage);
        }

        // Check if link already exists and is correct
        if (is_link($publicStorage)) {
            $target = readlink($publicStorage);
            if ($target === $storageAppPublic) {
                $this->info('✅ Symbolic link already exists and is correct');
                return true;
            } else {
                $this->warn("⚠️  Symbolic link exists but points to wrong location: {$target}");
                if ($this->confirm('Do you want to recreate it?')) {
                    unlink($publicStorage);
                } else {
                    return false;
                }
            }
        }

        // Create new symbolic link
        try {
            symlink($storageAppPublic, $publicStorage);
            $this->info('✅ Symbolic link created successfully');
            return true;
        } catch (\Exception $e) {
            $this->error("❌ Failed to create symbolic link: {$e->getMessage()}");
            return false;
        }
    }

    private function checkPermissions()
    {
        $this->info('🔐 Checking permissions...');

        $paths = [
            storage_path('app/public'),
            storage_path('app/public/vehicles'),
            public_path('storage')
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                $perms = substr(sprintf('%o', fileperms($path)), -4);
                $writable = is_writable($path);

                $status = $writable ? '✅' : '❌';
                $this->info("{$status} {$path} - Permissions: {$perms}, Writable: " . ($writable ? 'Yes' : 'No'));
            }
        }
    }

    private function testImageAccess()
    {
        $this->info('🧪 Testing image access...');

        $vehiclesPath = storage_path('app/public/vehicles');
        $files = File::files($vehiclesPath);

        if (empty($files)) {
            $this->warn('⚠️  No vehicle images found to test');
            return;
        }

        $sampleFile = basename($files[0]);
        $samplePath = "vehicles/{$sampleFile}";

        // Test Storage URL
        $storageUrl = Storage::disk('public')->url($samplePath);
        $this->info("📷 Sample image URL: {$storageUrl}");

        // Test if file exists
        $fullPath = storage_path("app/public/{$samplePath}");
        if (file_exists($fullPath)) {
            $this->info("✅ Sample image file exists: {$fullPath}");
        } else {
            $this->error("❌ Sample image file not found: {$fullPath}");
        }

        // Test symbolic link
        $publicPath = public_path("storage/{$samplePath}");
        if (file_exists($publicPath)) {
            $this->info("✅ Sample image accessible via public path: {$publicPath}");
        } else {
            $this->error("❌ Sample image not accessible via public path: {$publicPath}");
        }
    }
}
