<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestSimpleEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:simple-email {email} {--subject= : Custom subject}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a very simple test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $subject = $this->option('subject') ?: 'Simple Test - ' . date('H:i:s');

        $this->info('📧 Sending Simple Test Email...');
        $this->line('To: ' . $email);
        $this->line('Subject: ' . $subject);
        $this->newLine();

        try {
            // Test 1: Plain text email
            $this->info('📝 Test 1: Plain Text Email');
            Mail::raw('This is a simple test email from Luxuria UAE. Sent at: ' . date('Y-m-d H:i:s'), function($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject . ' (Plain Text)');
            });
            $this->info('✅ Plain text email sent successfully');
            $this->newLine();

            // Test 2: Simple HTML email
            $this->info('🌐 Test 2: Simple HTML Email');
            Mail::html('<h1>Test Email</h1><p>This is a simple HTML test email from Luxuria UAE.</p><p>Sent at: ' . date('Y-m-d H:i:s') . '</p>', function($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject . ' (HTML)');
            });
            $this->info('✅ HTML email sent successfully');
            $this->newLine();

            // Test 3: Email with custom from
            $this->info('📤 Test 3: Custom From Email');
            Mail::raw('This email has a custom from address. Sent at: ' . date('Y-m-d H:i:s'), function($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject . ' (Custom From)')
                        ->from('info@rentluxuria.com', 'Luxuria UAE Info');
            });
            $this->info('✅ Custom from email sent successfully');
            $this->newLine();

            // Test 4: Email with different content
            $this->info('📋 Test 4: Different Content Email');
            $content = "Hello,\n\nThis is a different test email with:\n";
            $content .= "- Multiple lines\n";
            $content .= "- Special characters: áéíóú\n";
            $content .= "- Numbers: 123456\n";
            $content .= "- Symbols: @#$%^&*()\n\n";
            $content .= "Sent at: " . date('Y-m-d H:i:s') . "\n";
            $content .= "From: Luxuria UAE System";

            Mail::raw($content, function($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject . ' (Special Content)');
            });
            $this->info('✅ Special content email sent successfully');

        } catch (\Exception $e) {
            $this->error('❌ Email sending failed: ' . $e->getMessage());
            Log::error('Simple email test failed', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        $this->newLine();
        $this->info('🎉 All test emails sent successfully!');
        $this->newLine();
        $this->info('📋 Next Steps:');
        $this->line('1. Check your email inbox: ' . $email);
        $this->line('2. Check spam/junk folder');
        $this->line('3. Wait 5-10 minutes for delivery');
        $this->line('4. If still not received, check Mailgun Dashboard');

        return 0;
    }
}
