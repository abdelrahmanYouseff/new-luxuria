<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\BookingPointsService;
use App\Services\BookingInvoiceService;
use App\Services\PointSysService;

class TestMobilePayment extends Command
{
    protected $signature = 'test:mobile-payment {--booking-id=} {--user-id=}';
    protected $description = 'Test mobile payment flow';

    public function handle()
    {
        $this->info('🧪 Testing Mobile Payment Flow...');

        $bookingId = $this->option('booking-id');
        $userId = $this->option('user-id');

        if (!$bookingId) {
            $booking = Booking::where('status', 'pending')->first();
            if (!$booking) {
                $this->error('No pending booking found. Please provide --booking-id');
                return 1;
            }
            $bookingId = $booking->id;
        }

        $booking = Booking::with(['user', 'vehicle'])->find($bookingId);
        if (!$booking) {
            $this->error("Booking with ID {$bookingId} not found.");
            return 1;
        }

        $this->info("📅 Testing with booking #{$booking->id}");
        $this->info("👤 User: {$booking->user->name} ({$booking->user->email})");
        $this->info("🚗 Vehicle: {$booking->vehicle->make} {$booking->vehicle->model}");
        $this->info("💰 Amount: {$booking->total_amount} AED");
        $this->info("📊 Points to earn: {$booking->points_earned}");
        $this->info("🔗 PointSys ID: {$booking->user->pointsys_customer_id}");
        $this->info("📈 Current status: {$booking->status}");

        // Step 1: Confirm payment (simulate mobile payment)
        $this->info("\n💳 Step 1: Confirming payment...");

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $this->info("✅ Payment confirmed!");

        // Step 2: Add points
        $this->info("\n🎯 Step 2: Adding points...");

        $bookingPointsService = new BookingPointsService(new PointSysService());
        $pointsResult = $bookingPointsService->addPointsToCustomer($booking);

        if ($pointsResult['success']) {
            $this->info("✅ Points added successfully!");
            $this->info("📈 Points added: {$pointsResult['points_added']}");
            if (isset($pointsResult['transaction_id'])) {
                $this->info("🆔 Transaction ID: {$pointsResult['transaction_id']}");
            }
        } else {
            $this->error("❌ Failed to add points: {$pointsResult['message']}");
        }

        // Step 3: Create invoice
        $this->info("\n📄 Step 3: Creating invoice...");

        $bookingInvoiceService = new BookingInvoiceService();
        $invoiceResult = $bookingInvoiceService->createInvoice($booking);

        if ($invoiceResult['success']) {
            $this->info("✅ Invoice created successfully!");
            $this->info("📄 Invoice number: {$invoiceResult['invoice_number']}");
        } else {
            $this->error("❌ Failed to create invoice: {$invoiceResult['message']}");
        }

        // Step 4: Check final status
        $this->info("\n📊 Step 4: Final status check...");

        $booking->refresh();
        $this->info("📈 Final booking status: {$booking->status}");
        $this->info("💳 Points added to customer: " . ($booking->points_added_to_customer ? 'Yes' : 'No'));
        $this->info("📄 Points earned: {$booking->points_earned}");

        // Step 5: Test PointSys balance
        $this->info("\n🔗 Step 5: Checking PointSys balance...");

        if ($booking->user->pointsys_customer_id) {
            $pointSysService = new PointSysService();
            $balance = $pointSysService->getCustomerBalance($booking->user->pointsys_customer_id);

            if ($balance && isset($balance['data'])) {
                $points = $balance['data']['points_balance'] ?? 0;
                $this->info("✅ PointSys balance: {$points} points");
            } else {
                $this->warn("⚠️ Could not fetch PointSys balance");
            }
        } else {
            $this->warn("⚠️ User has no PointSys ID");
        }

        $this->info("\n✅ Mobile payment test completed!");

        return 0;
    }
}
