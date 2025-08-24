<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class QuickFixLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quick:fix-login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quick fix for login issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🚀 Quick fixing login issues...");

        // Clear caches
        $this->info("📝 Clearing caches...");
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->info("✅ Caches cleared");

        // Clear sessions
        $this->info("📝 Clearing sessions...");
        if (DB::getSchemaBuilder()->hasTable('sessions')) {
            DB::table('sessions')->delete();
            $this->info("✅ Database sessions cleared");
        }

        // Clear session files
        $sessionPath = storage_path('framework/sessions');
        if (is_dir($sessionPath)) {
            $files = glob($sessionPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            $this->info("✅ Session files cleared");
        }

        // Check admin users
        $adminCount = DB::table('users')->where('role', 'admin')->count();
        $this->info("📊 Admin users found: {$adminCount}");

        if ($adminCount === 0) {
            $this->info("📝 Creating admin users...");
            Artisan::call('db:seed', ['--class' => 'SimpleAdminUserSeeder']);
            $this->info("✅ Admin users created");
        }

        // Test login
        $this->info("📝 Testing login...");
        $result = Artisan::call('test:server-login', [
            'email' => 'admin@rentluxuria.com',
            'password' => 'password123'
        ]);

        if ($result === 0) {
            $this->info("✅ Login test: PASSED");
        } else {
            $this->error("❌ Login test: FAILED");
        }

        $this->info("🎯 Quick fix completed!");
        $this->info("🌐 Test at: https://rentluxuria.com/test-login");

        return 0;
    }
}
