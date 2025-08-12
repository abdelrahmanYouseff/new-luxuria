<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class TestEmailSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-setup {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration and send a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'test@example.com';

        $this->info('🔧 Testing Email Configuration...');
        $this->newLine();

        // Show current mail configuration
        $this->info('📧 Current Mail Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Mailer', Config::get('mail.default')],
                ['Host', Config::get('mail.mailers.' . Config::get('mail.default') . '.host')],
                ['Port', Config::get('mail.mailers.' . Config::get('mail.default') . '.port')],
                ['Encryption', Config::get('mail.mailers.' . Config::get('mail.default') . '.encryption')],
                ['From Address', Config::get('mail.from.address')],
                ['From Name', Config::get('mail.from.name')],
            ]
        );

        $this->newLine();

        // Check if mailer is set to log
        if (Config::get('mail.default') === 'log') {
            $this->warn('⚠️  WARNING: Mailer is set to "log" - emails will not be sent!');
            $this->warn('   To send real emails, change MAIL_MAILER to "smtp" in your .env file');
            $this->newLine();
        }

        // Test email sending
        $this->info('📤 Testing Email Sending...');

        try {
            Mail::raw('This is a test email from Luxuria UAE system.', function($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Luxuria UAE')
                        ->from(Config::get('mail.from.address'), Config::get('mail.from.name'));
            });

            if (Config::get('mail.default') === 'log') {
                $this->info('✅ Test email logged successfully (not sent)');
                $this->info('   Check storage/logs/laravel.log for the email content');
            } else {
                $this->info('✅ Test email sent successfully to: ' . $email);
            }

        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email: ' . $e->getMessage());
            $this->newLine();

            $this->info('🔍 Troubleshooting Tips:');
            $this->line('1. Check your .env file mail settings');
            $this->line('2. Verify SMTP credentials');
            $this->line('3. Check firewall settings');
            $this->line('4. For Gmail: Use App Password, not regular password');
            $this->line('5. Enable 2FA in Gmail if using App Password');

            return 1;
        }

        $this->newLine();
        $this->info('📋 Next Steps:');

        if (Config::get('mail.default') === 'log') {
            $this->line('1. Edit your .env file');
            $this->line('2. Change MAIL_MAILER from "log" to "smtp"');
            $this->line('3. Add your SMTP settings');
            $this->line('4. Run: php artisan config:clear');
            $this->line('5. Run this command again');
        } else {
            $this->line('1. Test booking email: php artisan test:booking-email');
            $this->line('2. Test coupon email: php artisan test:coupon-email');
            $this->line('3. Check your email inbox');
        }

        return 0;
    }
}
