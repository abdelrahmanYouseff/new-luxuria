<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupMailgunSMTP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:mailgun-smtp {--test : Test the configuration after setup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Mailgun SMTP configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Setting up Mailgun SMTP configuration...');
        $this->newLine();

        // Check if .env file exists
        if (!File::exists(base_path('.env'))) {
            $this->error('❌ .env file not found!');
            return 1;
        }

        // Read current .env file
        $envContent = File::get(base_path('.env'));

        // Mailgun SMTP settings (no hardcoded secrets)
        $host = env('MAIL_HOST', 'smtp.mailgun.org');
        $port = env('MAIL_PORT', '587');
        $username = env('MAIL_USERNAME', '');
        $fromAddress = env('MAIL_FROM_ADDRESS', '');
        $fromName = env('MAIL_FROM_NAME', config('app.name', 'Laravel'));
        $encryption = env('MAIL_ENCRYPTION', 'tls');
        $password = $this->secret('Enter MAIL_PASSWORD (hidden)');

        $smtpSettings = [
            'MAIL_MAILER=smtp',
            'MAIL_HOST=' . $host,
            'MAIL_PORT=' . $port,
            'MAIL_USERNAME=' . $username,
            'MAIL_PASSWORD=' . $password,
            'MAIL_ENCRYPTION=' . $encryption,
            'MAIL_FROM_ADDRESS=' . $fromAddress,
            'MAIL_FROM_NAME="' . $fromName . '"'
        ];

        $this->info('📝 Updating .env file...');

        // Update or add each setting
        foreach ($smtpSettings as $setting) {
            $key = explode('=', $setting)[0];
            $value = explode('=', $setting, 2)[1];

            if (preg_match("/^{$key}=/m", $envContent)) {
                // Update existing setting
                $envContent = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $envContent);
                $this->line("✅ Updated: {$key}");
            } else {
                // Add new setting
                $envContent .= "\n{$setting}";
                $this->line("✅ Added: {$key}");
            }
        }

        // Write back to .env file
        File::put(base_path('.env'), $envContent);

        $this->newLine();
        $this->info('✅ Mailgun SMTP configuration updated successfully!');
        $this->newLine();

        // Clear config cache
        $this->info('🗑️  Clearing configuration cache...');
        $this->call('config:clear');
        $this->call('cache:clear');

        $this->newLine();
        $this->info('📋 Configuration Summary:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Mailer', 'smtp'],
                ['Host', $host],
                ['Port', $port],
                ['Encryption', $encryption],
                ['Username', $username],
                ['From Address', $fromAddress],
                ['From Name', $fromName],
            ]
        );

        // Test configuration if requested
        if ($this->option('test')) {
            $this->newLine();
            $this->info('🧪 Testing configuration...');
            $this->call('test:email-setup');
        }

        $this->newLine();
        $this->info('📋 Next Steps:');
        $this->line('1. Test email sending: php artisan test:email-setup your-email@gmail.com');
        $this->line('2. Test booking email: php artisan test:booking-email');
        $this->line('3. Test coupon email: php artisan test:coupon-email');
        $this->line('4. Check Mailgun Dashboard: https://app.mailgun.com/');

        return 0;
    }
}
