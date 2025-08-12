<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class QuickMailgunFix extends Command
{
    protected $signature = 'quick:mailgun-fix {email}';
    protected $description = 'Quick fix for Mailgun email delivery';

    public function handle()
    {
        $email = $this->argument('email');

        $this->info('🚀 Quick Mailgun Fix...');
        $this->newLine();

        // Apply optimized Mailgun settings
        $settings = [
            'MAIL_MAILER=smtp',
            'MAIL_HOST=smtp.eu.mailgun.org',
            'MAIL_PORT=587',
            'MAIL_USERNAME=info@rentluxuria.com',
            'MAIL_PASSWORD=[REDACTED_MAILGUN_KEY]',
            'MAIL_ENCRYPTION=tls',
            'MAIL_FROM_ADDRESS=info@rentluxuria.com',
            'MAIL_FROM_NAME="Luxuria UAE"'
        ];

        $this->info('📝 Applying optimized Mailgun settings...');
        
        $envContent = File::get(base_path('.env'));
        
        foreach ($settings as $setting) {
            $key = explode('=', $setting)[0];
            $value = explode('=', $setting, 2)[1];
            
            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $envContent);
                $this->line("✅ Updated: {$key}");
            } else {
                $envContent .= "\n{$setting}";
                $this->line("✅ Added: {$key}");
            }
        }

        File::put(base_path('.env'), $envContent);
        
        // Clear cache
        $this->call('config:clear');
        $this->call('cache:clear');

        $this->newLine();
        $this->info('🧪 Testing email delivery...');

        // Send test emails with different configurations
        $tests = [
            [
                'name' => 'Test 1: Basic Email',
                'subject' => 'Mailgun Test 1 - ' . date('H:i:s'),
                'from' => 'info@rentluxuria.com'
            ],
            [
                'name' => 'Test 2: Different From',
                'subject' => 'Mailgun Test 2 - ' . date('H:i:s'),
                'from' => 'support@rentluxuria.com'
            ],
            [
                'name' => 'Test 3: Booking Style',
                'subject' => 'Booking Test - ' . date('H:i:s'),
                'from' => 'bookings@rentluxuria.com'
            ]
        ];

        foreach ($tests as $index => $test) {
            try {
                $this->info("📧 {$test['name']}...");
                
                Mail::raw("This is {$test['name']} from Luxuria UAE. Sent at: " . date('Y-m-d H:i:s'), function($message) use ($email, $test) {
                    $message->to($email)
                            ->subject($test['subject'])
                            ->from($test['from'], 'Luxuria UAE');
                });
                
                $this->info("✅ {$test['name']} sent successfully!");
                
                // Wait 3 seconds
                sleep(3);
                
                // Ask user
                if ($this->confirm("Did you receive {$test['name']}?")) {
                    $this->info("🎉 {$test['name']} works! This configuration is working.");
                    $this->newLine();
                    $this->info('📋 Next Steps:');
                    $this->line('1. The system is now working correctly');
                    $this->line('2. Booking emails will be sent automatically');
                    $this->line('3. Check your email for the test message');
                    return 0;
                }
                
            } catch (\Exception $e) {
                $this->error("❌ {$test['name']} failed: " . $e->getMessage());
            }
        }

        $this->error('❌ All tests failed. Checking Mailgun status...');
        $this->newLine();
        $this->info('🔍 Troubleshooting Steps:');
        $this->line('1. Check Mailgun Dashboard: https://app.mailgun.com/');
        $this->line('2. Verify domain: sandbox6786de7da9c849738bf4b50a2669b5f3.mailgun.org');
        $this->line('3. Check sending limits and account status');
        $this->line('4. Look for emails in spam/junk folder');
        $this->line('5. Wait 5-10 minutes for delivery');

        return 1;
    }
} 