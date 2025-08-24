<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\BookingPointsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AddPointsToExistingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:add-points {--dry-run : Show what would be done without actually doing it} {--booking-id= : Specific booking ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add points to existing confirmed bookings that don\'t have points added yet';

    /**
     * Execute the console command.
     */
    public function handle(BookingPointsService $bookingPointsService)
    {
        $this->info('🎯 Adding Points to Existing Bookings');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $specificBookingId = $this->option('booking-id');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual changes will be made');
            $this->newLine();
        }

        if ($specificBookingId) {
            $this->processSpecificBooking($specificBookingId, $bookingPointsService, $dryRun);
        } else {
            $this->processAllBookings($bookingPointsService, $dryRun);
        }
    }

    private function processSpecificBooking($bookingId, BookingPointsService $bookingPointsService, $dryRun)
    {
        $this->info("📋 Processing specific booking ID: {$bookingId}");

        $booking = Booking::with(['user', 'vehicle'])->find($bookingId);

        if (!$booking) {
            $this->error("❌ Booking with ID {$bookingId} not found");
            return;
        }

        $this->processBooking($booking, $bookingPointsService, $dryRun);
    }

    private function processAllBookings(BookingPointsService $bookingPointsService, $dryRun)
    {
        $this->info("📋 Finding confirmed bookings without points...");

        $bookings = Booking::with(['user', 'vehicle'])
            ->where('status', 'confirmed')
            ->where('points_added_to_customer', false)
            ->get();

        if ($bookings->isEmpty()) {
            $this->info("✅ No confirmed bookings found that need points added");
            return;
        }

        $this->info("Found {$bookings->count()} confirmed bookings without points:");
        $this->newLine();

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        $progressBar = $this->output->createProgressBar($bookings->count());
        $progressBar->start();

        foreach ($bookings as $booking) {
            $result = $this->processBooking($booking, $bookingPointsService, $dryRun, false);

            if ($result === 'success') {
                $successCount++;
            } elseif ($result === 'error') {
                $errorCount++;
            } else {
                $skippedCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info("📊 Summary:");
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Success', $successCount],
                ['❌ Errors', $errorCount],
                ['⏭️  Skipped', $skippedCount],
                ['📋 Total', $bookings->count()],
            ]
        );
    }

    private function processBooking(Booking $booking, BookingPointsService $bookingPointsService, $dryRun, $verbose = true)
    {
        if ($verbose) {
            $this->displayBookingInfo($booking);
        }

        // Check if points already added
        if ($booking->points_added_to_customer) {
            if ($verbose) {
                $this->info("   ⏭️  Points already added - skipping");
            }
            return 'skipped';
        }

        // Check if user is registered in PointSys
        if (!$booking->user->pointsys_customer_id) {
            if ($verbose) {
                $this->warn("   ⚠️  User not registered in PointSys - skipping");
                $this->line("   👤 User: {$booking->user->name} ({$booking->user->email})");
                $this->line("   🔗 PointSys Customer ID: Not registered");
            }
            return 'skipped';
        }

        // Calculate points
        $pointsToAdd = $bookingPointsService->calculatePointsForBooking($booking);

        if ($pointsToAdd <= 0) {
            if ($verbose) {
                $this->warn("   ⚠️  No points to add (0 days or invalid calculation)");
            }
            return 'skipped';
        }

        if ($verbose) {
            $this->info("   💰 Points to add: {$pointsToAdd} points");
        }

        if ($dryRun) {
            if ($verbose) {
                $this->info("   🔍 DRY RUN: Would add {$pointsToAdd} points to customer {$booking->user->pointsys_customer_id}");
            }
            return 'success';
        }

        // Actually add points
        $result = $bookingPointsService->addPointsToCustomer($booking);

        if ($result['success']) {
            if ($verbose) {
                $this->info("   ✅ Points added successfully: {$result['points_added']} points");
                $this->info("   📝 Message: {$result['message']}");
            }
            return 'success';
        } else {
            if ($verbose) {
                $this->error("   ❌ Failed to add points: {$result['message']}");
                if (isset($result['error'])) {
                    $this->error("   🔍 Error: {$result['error']}");
                }
            }
            return 'error';
        }
    }

    private function displayBookingInfo(Booking $booking)
    {
        $this->info("📋 Booking #{$booking->id}");
        $this->line("   User: {$booking->user->name} ({$booking->user->email})");
        $this->line("   Vehicle: {$booking->vehicle->make} {$booking->vehicle->model}");
        $this->line("   Period: {$booking->start_date->format('d/m/Y')} - {$booking->end_date->format('d/m/Y')}");
        $this->line("   Days: {$booking->total_days}");
        $this->line("   Amount: AED {$booking->total_amount}");
        $this->line("   Status: {$booking->status}");
        $this->line("   Points Earned: {$booking->points_earned}");
        $this->line("   Points Added: " . ($booking->points_added_to_customer ? 'Yes' : 'No'));
        $this->line("   PointSys Customer ID: " . ($booking->user->pointsys_customer_id ?: 'Not registered'));
    }
}
