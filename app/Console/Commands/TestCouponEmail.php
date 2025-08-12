<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmationMail;
use App\Models\CouponInvoice;

class TestCouponEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:coupon-email {invoice_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test coupon payment confirmation email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceId = $this->argument('invoice_id');

        if ($invoiceId) {
            $invoice = CouponInvoice::find($invoiceId);
            if (!$invoice) {
                $this->error("Invoice with ID {$invoiceId} not found!");
                return 1;
            }
        } else {
            $invoice = CouponInvoice::first();
            if (!$invoice) {
                $this->error("No invoices found in database!");
                return 1;
            }
        }

        $this->info("Testing coupon payment confirmation email for:");
        $this->info("Invoice ID: {$invoice->id}");
        $this->info("Invoice Number: {$invoice->invoice_number}");
        $this->info("Customer: {$invoice->customer_name}");
        $this->info("Coupon: {$invoice->coupon_name}");
        $this->info("Amount: AED {$invoice->amount}");

        try {
            $couponDetails = [
                'name' => $invoice->coupon_name,
                'amount' => $invoice->amount,
                'currency' => $invoice->currency,
            ];

            Mail::to($invoice->customer_email)->send(new PaymentConfirmationMail($invoice, $couponDetails));
            $this->info("✅ Coupon payment confirmation email sent successfully to {$invoice->customer_email}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to send coupon payment confirmation email: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
