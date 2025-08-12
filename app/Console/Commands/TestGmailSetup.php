<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class TestGmailSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:gmail-setup {email} {--apply : Apply Gmail settings to .env}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Gmail SMTP configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info('📧 Testing Gmail SMTP Configuration...');
        $this->newLine();

        // Gmail SMTP settings
        $gmailSettings = [
            'MAIL_MAILER=smtp',
            'MAIL_HOST=smtp.gmail.com',
            'MAIL_PORT=587',
            'MAIL_USERNAME=abdelrahmanyouseff@gmail.com',
            'MAIL_PASSWORD=your-app-password-here',
            'MAIL_ENCRYPTION=tls',
            'MAIL_FROM_ADDRESS=abdelrahmanyouseff@gmail.com',
            'MAIL_FROM_NAME="Luxuria UAE"'
        ];

        $this->info('📋 Gmail SMTP Settings:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Mailer', 'smtp'],
                ['Host', 'smtp.gmail.com'],
                ['Port', '587'],
                ['Encryption', 'tls'],
                ['Username', 'abdelrahmanyouseff@gmail.com'],
                ['From Address', 'abdelrahmanyouseff@gmail.com'],
                ['From Name', 'Luxuria UAE'],
            ]
        );

        if ($this->option('apply')) {
            $this->newLine();
            $this->info('⚠️  IMPORTANT: Before applying Gmail settings:');
            $this->line('1. Enable 2-Factor Authentication in your Gmail account');
            $this->line('2. Generate an App Password:');
            $this->line('   - Go to Google Account Settings');
            $this->line('   - Security > 2-Step Verification > App passwords');
            $this->line('   - Generate password for "Mail"');
            $this->line('3. Replace "your-app-password-here" with the generated password');
            $this->newLine();

            if (!$this->confirm('Have you generated the App Password?')) {
                $this->error('Please generate App Password first!');
                return 1;
            }

            $appPassword = $this->secret('Enter your Gmail App Password:');
            
            if (empty($appPassword)) {
                $this->error('App Password is required!');
                return 1;
            }

            // Update .env file
            $envContent = File::get(base_path('.env'));
            
            foreach ($gmailSettings as $setting) {
                $key = explode('=', $setting)[0];
                $value = explode('=', $setting, 2)[1];
                
                // Replace app password
                if ($key === 'MAIL_PASSWORD') {
                    $value = $appPassword;
                }
                
                if (preg_match("/^{$key}=/m", $envContent)) {
                    $envContent = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $envContent);
                    $this->line("✅ Updated: {$key}");
                } else {
                    $envContent .= "\n{$key}={$value}";
                    $this->line("✅ Added: {$key}");
                }
            }

            File::put(base_path('.env'), $envContent);
            
            $this->newLine();
            $this->info('✅ Gmail settings applied to .env file');
            
            // Clear config cache
            $this->call('config:clear');
            $this->call('cache:clear');
            
            $this->newLine();
            $this->info('🧪 Testing Gmail email sending...');
            
            try {
                Mail::raw('Gmail SMTP test - ' . date('Y-m-d H:i:s'), function($message) use ($email) {
                    $message->to($email)
                            ->subject('Gmail Test - ' . date('H:i:s'));
                });
                
                $this->info('✅ Gmail test email sent successfully!');
            } catch (\Exception $e) {
                $this->error('❌ Gmail test failed: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->newLine();
            $this->info('📋 To apply Gmail settings, run:');
            $this->line('php artisan test:gmail-setup ' . $email . ' --apply');
            $this->newLine();
            $this->info('📋 Gmail Setup Instructions:');
            $this->line('1. Enable 2-Factor Authentication in Gmail');
            $this->line('2. Generate App Password for "Mail"');
            $this->line('3. Use the App Password (not your regular password)');
            $this->line('4. Run the command with --apply flag');
        }

        return 0;
    }
} 