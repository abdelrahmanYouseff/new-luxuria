<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\Exception\ApiErrorException;

class TestStripeConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Stripe connection and show current payment mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Stripe Connection...');
        $this->newLine();

        // Get Stripe keys from config
        $publicKey = config('services.stripe.public_key');
        $secretKey = config('services.stripe.secret_key');
        $webhookSecret = config('services.stripe.webhook_secret');

        // Display current keys (masked)
        $this->info('📋 Current Configuration:');
        $this->line('Public Key: ' . $this->maskKey($publicKey));
        $this->line('Secret Key: ' . $this->maskKey($secretKey));
        $this->line('Webhook Secret: ' . $this->maskKey($webhookSecret));
        $this->newLine();

        // Check if using mock mode
        $isPlaceholder = !$secretKey || str_contains($secretKey, 'your_secret_key_here');

        if ($isPlaceholder) {
            $this->warn('⚠️  MOCK MODE DETECTED');
            $this->line('The system is using placeholder Stripe keys.');
            $this->line('Payment system will use mock/testing mode.');
            $this->newLine();

            $this->info('🧪 Mock Mode Features:');
            $this->line('✅ Mock payment page (/mock-payment)');
            $this->line('✅ Simulated payment processing');
            $this->line('✅ Points addition (500 points per purchase)');
            $this->line('✅ Invoice creation');
            $this->line('✅ No real money transactions');

            $this->newLine();
            $this->comment('To use real Stripe, update your .env file with actual Stripe keys.');
            return;
        }

        // Test real Stripe connection
        $this->info('🔗 Testing Real Stripe Connection...');

        try {
            Stripe::setApiKey($secretKey);
            $account = Account::retrieve();

            $this->info('✅ SUCCESS: Connected to Stripe!');
            $this->newLine();

            $this->info('🏢 Account Details:');
            $this->line('Account ID: ' . $account->id);
            $this->line('Display Name: ' . ($account->display_name ?? 'N/A'));
            $this->line('Country: ' . ($account->country ?? 'N/A'));
            $this->line('Default Currency: ' . ($account->default_currency ?? 'N/A'));
            $this->line('Charges Enabled: ' . ($account->charges_enabled ? 'Yes' : 'No'));
            $this->line('Payouts Enabled: ' . ($account->payouts_enabled ? 'Yes' : 'No'));

            // Determine mode
            $mode = str_contains($secretKey, '_test_') ? 'TEST' : 'LIVE';
            $modeColor = $mode === 'TEST' ? 'yellow' : 'red';

            $this->newLine();
            $this->line('<fg=' . $modeColor . '>🎯 MODE: ' . $mode . '</fg=' . $modeColor . '>');

            if ($mode === 'TEST') {
                $this->line('<fg=yellow>This is TEST mode - no real money will be charged.</fg=yellow>');
                $this->newLine();
                $this->info('🧪 Test Card Numbers:');
                $this->line('Visa: 4242 4242 4242 4242');
                $this->line('Mastercard: 5555 5555 5555 4444');
                $this->line('Declined: 4000 0000 0000 0002');
            } else {
                $this->line('<fg=red>⚠️  This is LIVE mode - real money will be charged!</fg=red>');
            }

            $this->newLine();
            $this->info('🎯 Payment System Status:');
            $this->line('✅ Real Stripe payments enabled');
            $this->line('✅ Automatic points addition (500 per purchase)');
            $this->line('✅ Invoice generation');
            $this->line('✅ Webhook support (if configured)');

        } catch (ApiErrorException $e) {
            $this->error('❌ STRIPE CONNECTION FAILED');
            $this->line('Error: ' . $e->getMessage());
            $this->newLine();

            $this->warn('💡 Possible issues:');
            $this->line('• Invalid API key');
            $this->line('• API key has insufficient permissions');
            $this->line('• Network connectivity issues');
            $this->line('• Stripe account is restricted');

            $this->newLine();
            $this->info('🔄 The system will fallback to MOCK mode for now.');

        } catch (\Exception $e) {
            $this->error('❌ UNEXPECTED ERROR');
            $this->line('Error: ' . $e->getMessage());
            $this->newLine();
            $this->info('🔄 The system will fallback to MOCK mode for now.');
        }

        $this->newLine();
        $this->comment('💡 Test your payment system at: http://127.0.0.1:8001/coupons');
    }

    /**
     * Mask sensitive keys for display
     */
    private function maskKey($key)
    {
        if (!$key) {
            return '<fg=red>NOT SET</fg=red>';
        }

        if (str_contains($key, 'your_') && str_contains($key, '_key_here')) {
            return '<fg=yellow>PLACEHOLDER</fg=yellow>';
        }

        if (strlen($key) > 10) {
            return substr($key, 0, 8) . str_repeat('*', max(0, strlen($key) - 12)) . substr($key, -4);
        }

        return $key;
    }
}
