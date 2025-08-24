<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class FixServerLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:server-login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix server login issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔧 Fixing server login issues...");

        // Step 1: Clear all caches
        $this->info("📝 Step 1: Clearing caches...");
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('session:clear');
        $this->info("✅ Caches cleared");

        // Step 2: Check database connection
        $this->info("📝 Step 2: Checking database connection...");
        try {
            DB::connection()->getPdo();
            $this->info("✅ Database connection: OK");
        } catch (\Exception $e) {
            $this->error("❌ Database connection failed: " . $e->getMessage());
            return 1;
        }

        // Step 3: Check sessions table
        $this->info("📝 Step 3: Checking sessions table...");
        if (DB::getSchemaBuilder()->hasTable('sessions')) {
            $this->info("✅ Sessions table: EXISTS");

            // Clear old sessions
            DB::table('sessions')->delete();
            $this->info("✅ Old sessions cleared");
        } else {
            $this->error("❌ Sessions table: MISSING");
            $this->info("📝 Creating sessions table...");
            Artisan::call('session:table');
            Artisan::call('migrate');
            $this->info("✅ Sessions table created");
        }

        // Step 4: Check admin users
        $this->info("📝 Step 4: Checking admin users...");
        $adminCount = DB::table('users')->where('role', 'admin')->count();
        $this->info("📊 Admin users found: {$adminCount}");

        if ($adminCount === 0) {
            $this->info("📝 Creating admin users...");
            Artisan::call('db:seed', ['--class' => 'SimpleAdminUserSeeder']);
            $this->info("✅ Admin users created");
        }

        // Step 5: Check session configuration
        $this->info("📝 Step 5: Checking session configuration...");
        $this->info("- Driver: " . config('session.driver'));
        $this->info("- Lifetime: " . config('session.lifetime') . " minutes");
        $this->info("- Encrypt: " . (config('session.encrypt') ? 'YES' : 'NO'));

        // Step 6: Test login
        $this->info("📝 Step 6: Testing login...");
        $result = Artisan::call('test:server-login', [
            'email' => 'admin@rentluxuria.com',
            'password' => 'password123'
        ]);

        if ($result === 0) {
            $this->info("✅ Login test: PASSED");
        } else {
            $this->error("❌ Login test: FAILED");
        }

        // Step 7: Recommendations
        $this->info("📝 Step 7: Recommendations...");
        $this->info("🔄 Please restart the server:");
        $this->info("   sudo systemctl restart php8.3-fpm");
        $this->info("   sudo systemctl restart nginx");

        $this->info("🌐 Test the login at:");
        $this->info("   https://rentluxuria.com/test-login");

        $this->info("✅ Server login fix completed!");

        return 0;
    }
}
