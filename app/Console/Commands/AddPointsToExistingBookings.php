<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\BookingPointsService;
use App\Services\PointSysService;

class AddPointsToExistingBookings extends Command
{
    protected $signature = 'add:points-to-bookings {--booking-id=} {--dry-run}';
    protected $description = 'Add points to existing confirmed bookings that haven\'t had points added yet';

    public function handle()
    {
        $this->info('💳 Adding points to existing bookings...');

        $bookingId = $this->option('booking-id');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual changes will be made');
        }

        // Get bookings that are confirmed but haven't had points added
        $query = Booking::where('status', 'confirmed')
            ->where('points_added_to_customer', false)
            ->with(['user', 'vehicle']);

        if ($bookingId) {
            $query->where('id', $bookingId);
        }

        $bookings = $query->get();

        if ($bookings->isEmpty()) {
            $this->info('✅ No bookings found that need points added');
            return 0;
        }

        $this->info("📊 Found {$bookings->count()} bookings to process");

        $bookingPointsService = new BookingPointsService(new PointSysService());
        $success = 0;
        $failed = 0;

        foreach ($bookings as $booking) {
            $this->line("📅 Processing Booking #{$booking->id}");
            $this->line("   User: {$booking->user->name} ({$booking->user->email})");
            $this->line("   Vehicle: {$booking->vehicle->make} {$booking->vehicle->model}");
            $this->line("   Days: {$booking->total_days}");
            $this->line("   Points to earn: {$booking->points_earned}");
            $this->line("   PointSys ID: {$booking->user->pointsys_customer_id}");

            // Check if user has valid PointSys ID
            if (!$booking->user->pointsys_customer_id) {
                $this->line("   ❌ User has no PointSys ID - skipping");
                $failed++;
                continue;
            }

            if ($dryRun) {
                $this->line("   🔍 Would add {$booking->points_earned} points");
                $success++;
            } else {
                // Add points to customer
                $result = $bookingPointsService->addPointsToCustomer($booking);

                if ($result['success']) {
                    $this->line("   ✅ Points added successfully!");
                    $this->line("   📈 Points added: {$result['points_added']}");
                    if (isset($result['transaction_id'])) {
                        $this->line("   🆔 Transaction ID: {$result['transaction_id']}");
                    }
                    $success++;
                } else {
                    $this->line("   ❌ Failed to add points: {$result['message']}");
                    $failed++;
                }
            }

            $this->line(""); // Empty line for readability
        }

        $this->info("📈 Summary:");
        $this->line("   ✅ Success: {$success} bookings");
        $this->line("   ❌ Failed: {$failed} bookings");
        $this->line("   📊 Total: " . ($success + $failed) . " bookings processed");

        if ($dryRun) {
            $this->warn("🔍 This was a dry run. Run without --dry-run to actually add points.");
        }

        return 0;
    }
}
