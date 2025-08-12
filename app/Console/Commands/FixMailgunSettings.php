<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class FixMailgunSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:mailgun-settings {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Mailgun settings and test email delivery';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info('🔧 Fixing Mailgun Settings...');
        $this->newLine();

        // Try different Mailgun configurations
        $configurations = [
            [
                'name' => 'Configuration 1: Standard SMTP',
                'settings' => [
                    'MAIL_MAILER=smtp',
                    'MAIL_HOST=smtp.eu.mailgun.org',
                    'MAIL_PORT=587',
                    'MAIL_USERNAME=info@rentluxuria.com',
                    'MAIL_PASSWORD=[REDACTED_MAILGUN_KEY]',
                    'MAIL_ENCRYPTION=tls',
                    'MAIL_FROM_ADDRESS=info@rentluxuria.com',
                    'MAIL_FROM_NAME="Luxuria UAE"'
                ]
            ],
            [
                'name' => 'Configuration 2: Different From Address',
                'settings' => [
                    'MAIL_MAILER=smtp',
                    'MAIL_HOST=smtp.eu.mailgun.org',
                    'MAIL_PORT=587',
                    'MAIL_USERNAME=info@rentluxuria.com',
                    'MAIL_PASSWORD=[REDACTED_MAILGUN_KEY]',
                    'MAIL_ENCRYPTION=tls',
                    'MAIL_FROM_ADDRESS=support@rentluxuria.com',
                    'MAIL_FROM_NAME="Luxuria UAE Support"'
                ]
            ],
            [
                'name' => 'Configuration 3: Port 465 with SSL',
                'settings' => [
                    'MAIL_MAILER=smtp',
                    'MAIL_HOST=smtp.eu.mailgun.org',
                    'MAIL_PORT=465',
                    'MAIL_USERNAME=info@rentluxuria.com',
                    'MAIL_PASSWORD=[REDACTED_MAILGUN_KEY]',
                    'MAIL_ENCRYPTION=ssl',
                    'MAIL_FROM_ADDRESS=bookings@rentluxuria.com',
                    'MAIL_FROM_NAME="Luxuria UAE Bookings"'
                ]
            ]
        ];

        foreach ($configurations as $index => $config) {
            $this->info('🧪 Testing ' . $config['name']);
            $this->newLine();

            // Apply configuration
            $envContent = File::get(base_path('.env'));
            
            foreach ($config['settings'] as $setting) {
                $key = explode('=', $setting)[0];
                $value = explode('=', $setting, 2)[1];
                
                if (preg_match("/^{$key}=/m", $envContent)) {
                    $envContent = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $envContent);
                } else {
                    $envContent .= "\n{$setting}";
                }
            }

            File::put(base_path('.env'), $envContent);
            
            // Clear config cache
            $this->call('config:clear');
            $this->call('cache:clear');

            // Test email sending
            try {
                $this->info('📧 Sending test email...');
                
                Mail::raw('Mailgun Configuration Test ' . ($index + 1) . ' - ' . date('Y-m-d H:i:s'), function($message) use ($email, $index) {
                    $message->to($email)
                            ->subject('Mailgun Test ' . ($index + 1) . ' - ' . date('H:i:s'));
                });
                
                $this->info('✅ Configuration ' . ($index + 1) . ' - Email sent successfully!');
                $this->newLine();
                
                // Wait a moment
                sleep(2);
                
                // Ask user if they received the email
                if ($this->confirm('Did you receive the email from Configuration ' . ($index + 1) . '?')) {
                    $this->info('🎉 Configuration ' . ($index + 1) . ' works! Keeping these settings.');
                    return 0;
                } else {
                    $this->warn('Configuration ' . ($index + 1) . ' failed. Trying next configuration...');
                    $this->newLine();
                }
                
            } catch (\Exception $e) {
                $this->error('❌ Configuration ' . ($index + 1) . ' failed: ' . $e->getMessage());
                $this->newLine();
            }
        }

        $this->error('❌ All configurations failed. Please check Mailgun settings manually.');
        $this->newLine();
        $this->info('📋 Manual Steps:');
        $this->line('1. Check Mailgun Dashboard: https://app.mailgun.com/');
        $this->line('2. Verify domain settings');
        $this->line('3. Check sending limits');
        $this->line('4. Consider using a different email provider');

        return 1;
    }
} 