<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupMailgun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:mailgun {--test : Test the configuration after setup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Mailgun email configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Setting up Mailgun configuration...');
        $this->newLine();

        // Check if .env file exists
        if (!File::exists(base_path('.env'))) {
            $this->error('❌ .env file not found!');
            return 1;
        }

        // Read current .env file
        $envContent = File::get(base_path('.env'));

        // Mailgun settings to add/update
        $mailgunSettings = [
            'MAIL_MAILER=mailgun',
            'MAILGUN_DOMAIN=sandbox6786de7da9c849738bf4b50a2669b5f3.mailgun.org',
            'MAILGUN_SECRET=[REDACTED_MAILGUN_KEY]',
            'MAILGUN_ENDPOINT=api.eu.mailgun.org',
            'MAIL_FROM_ADDRESS=noreply@rentluxuria.com',
            'MAIL_FROM_NAME="Luxuria UAE"',
            'MAIL_HOST=smtp.eu.mailgun.org',
            'MAIL_PORT=587',
            'MAIL_USERNAME=info@rentluxuria.com',
            'MAIL_PASSWORD=[REDACTED_MAILGUN_KEY]',
            'MAIL_ENCRYPTION=tls'
        ];

        $this->info('📝 Updating .env file...');

        // Update or add each setting
        foreach ($mailgunSettings as $setting) {
            $key = explode('=', $setting)[0];
            $value = explode('=', $setting, 2)[1];

            // Remove quotes from value for comparison
            $cleanValue = trim($value, '"');

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
        $this->info('✅ Mailgun configuration updated successfully!');
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
                ['Mailer', 'mailgun'],
                ['Domain', 'sandbox6786de7da9c849738bf4b50a2669b5f3.mailgun.org'],
                ['Endpoint', 'api.eu.mailgun.org'],
                ['From Address', 'noreply@rentluxuria.com'],
                ['From Name', 'Luxuria UAE'],
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
