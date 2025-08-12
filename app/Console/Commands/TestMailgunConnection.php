<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestMailgunConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mailgun-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Mailgun API connection and SMTP settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Testing Mailgun Connection...');
        $this->newLine();

        // Test 1: Check configuration
        $this->info('📋 Configuration Check:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Domain', config('services.mailgun.domain')],
                ['Secret', substr(config('services.mailgun.secret'), 0, 10) . '...'],
                ['Endpoint', config('services.mailgun.endpoint')],
                ['SMTP Host', config('mail.mailers.smtp.host')],
                ['SMTP Port', config('mail.mailers.smtp.port')],
                ['SMTP Username', config('mail.mailers.smtp.username')],
                ['From Address', config('mail.from.address')],
            ]
        );

        // Test 2: Test SMTP connection
        $this->newLine();
        $this->info('🔌 Testing SMTP Connection...');

        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');

        $connection = @fsockopen($host, $port, $errno, $errstr, 10);

        if ($connection) {
            $this->info('✅ SMTP connection successful');
            fclose($connection);
        } else {
            $this->error('❌ SMTP connection failed: ' . $errstr);
        }

        // Test 3: Test Mailgun API
        $this->newLine();
        $this->info('🌐 Testing Mailgun API...');

        $domain = config('services.mailgun.domain');
        $secret = config('services.mailgun.secret');
        $endpoint = config('services.mailgun.endpoint');

        if (!$domain || !$secret) {
            $this->error('❌ Mailgun credentials not configured');
            return 1;
        }

        try {
            $response = Http::withBasicAuth('api', $secret)
                ->get("https://{$endpoint}/v3/{$domain}/events", [
                    'limit' => 1
                ]);

            if ($response->successful()) {
                $this->info('✅ Mailgun API connection successful');
                $this->line('Response status: ' . $response->status());
            } else {
                $this->error('❌ Mailgun API connection failed');
                $this->line('Status: ' . $response->status());
                $this->line('Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ Mailgun API error: ' . $e->getMessage());
        }

        // Test 4: Send test email
        $this->newLine();
        $this->info('📧 Testing Email Sending...');

        try {
            Mail::raw('Mailgun connection test - ' . date('Y-m-d H:i:s'), function($message) {
                $message->to('abdelrahmanyouseff@gmail.com')
                        ->subject('Mailgun Test - ' . date('H:i:s'));
            });

            $this->info('✅ Test email sent successfully');
        } catch (\Exception $e) {
            $this->error('❌ Test email failed: ' . $e->getMessage());
        }

        // Test 5: Check recent events
        $this->newLine();
        $this->info('📊 Recent Email Events:');

        try {
            $response = Http::withBasicAuth('api', $secret)
                ->get("https://{$endpoint}/v3/{$domain}/events", [
                    'limit' => 5,
                    'event' => 'delivered'
                ]);

            if ($response->successful()) {
                $events = $response->json('items', []);
                if (empty($events)) {
                    $this->warn('No recent delivered events found');
                } else {
                    foreach ($events as $event) {
                        $this->line('✅ ' . $event['message']['headers']['to'] . ' - ' . $event['timestamp']);
                    }
                }
            } else {
                $this->error('Failed to fetch events: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('Error fetching events: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('📋 Recommendations:');
        $this->line('1. Check Mailgun Dashboard: https://app.mailgun.com/');
        $this->line('2. Verify domain settings in Mailgun');
        $this->line('3. Check if emails are being delivered to spam');
        $this->line('4. Monitor logs: php artisan monitor:emails --follow');

        return 0;
    }
}
