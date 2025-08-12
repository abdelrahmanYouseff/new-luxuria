<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmationMail;
use App\Models\CouponInvoice;
use App\Models\User;

class TestPaymentEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:payment-email {email : The email address to send to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test payment confirmation email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        // Create a dummy invoice for testing
        $dummyInvoice = new CouponInvoice([
            'invoice_number' => 'INV-TEST-' . now()->format('YmdHis'),
            'coupon_name' => 'Test Luxury Car Coupon - 25% OFF',
            'amount' => 150.00,
            'currency' => 'AED',
            'customer_name' => 'Test Customer',
            'customer_email' => $email,
            'paid_at' => now(),
        ]);

        $couponDetails = [
            'code' => 'LUXTEST2024',
            'discount_type' => 'percentage',
            'discount_value' => '25',
        ];

        try {
            $this->info("Sending test payment confirmation email to: {$email}");

            Mail::to($email)->send(new PaymentConfirmationMail($dummyInvoice, $couponDetails));

            $this->info("✅ Email sent successfully!");
            $this->info("Check your email inbox or Laravel logs for the email content.");

            if (config('mail.default') === 'log') {
                $this->warn("📧 Mail is configured to use 'log' driver. Check storage/logs/laravel.log for email content.");
            }

        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
