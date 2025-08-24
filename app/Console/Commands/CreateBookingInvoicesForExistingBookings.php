<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\BookingInvoiceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateBookingInvoicesForExistingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:create-invoices {--dry-run : Show what would be done without actually doing it} {--booking-id= : Specific booking ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create invoices for existing confirmed bookings that don\'t have invoices yet';

    /**
     * Execute the console command.
     */
    public function handle(BookingInvoiceService $bookingInvoiceService)
    {
        $this->info('🎯 Creating Invoices for Existing Bookings');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $specificBookingId = $this->option('booking-id');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual changes will be made');
            $this->newLine();
        }

        if ($specificBookingId) {
            $this->processSpecificBooking($specificBookingId, $bookingInvoiceService, $dryRun);
        } else {
            $this->processAllBookings($bookingInvoiceService, $dryRun);
        }
    }

    private function processSpecificBooking($bookingId, BookingInvoiceService $bookingInvoiceService, $dryRun)
    {
        $this->info("📋 Processing specific booking ID: {$bookingId}");
        
        $booking = Booking::with(['user', 'vehicle'])->find($bookingId);
        
        if (!$booking) {
            $this->error("❌ Booking with ID {$bookingId} not found");
            return;
        }

        $this->processBooking($booking, $bookingInvoiceService, $dryRun);
    }

    private function processAllBookings(BookingInvoiceService $bookingInvoiceService, $dryRun)
    {
        $this->info("📋 Finding confirmed bookings without invoices...");
        
        // Get bookings that don't have invoices yet
        $bookings = Booking::with(['user', 'vehicle'])
            ->where('status', 'confirmed')
            ->whereDoesntHave('bookingInvoices')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info("✅ No confirmed bookings found that need invoices created");
            return;
        }

        $this->info("Found {$bookings->count()} confirmed bookings without invoices:");
        $this->newLine();

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        $progressBar = $this->output->createProgressBar($bookings->count());
        $progressBar->start();

        foreach ($bookings as $booking) {
            $result = $this->processBooking($booking, $bookingInvoiceService, $dryRun, false);
            
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

    private function processBooking(Booking $booking, BookingInvoiceService $bookingInvoiceService, $dryRun, $verbose = true)
    {
        if ($verbose) {
            $this->displayBookingInfo($booking);
        }

        // Check if invoice already exists
        if ($booking->bookingInvoices()->exists()) {
            if ($verbose) {
                $this->info("   ⏭️  Invoice already exists - skipping");
            }
            return 'skipped';
        }

        // Check if booking is confirmed
        if ($booking->status !== 'confirmed') {
            if ($verbose) {
                $this->warn("   ⚠️  Booking not confirmed (status: {$booking->status}) - skipping");
            }
            return 'skipped';
        }

        // Check if vehicle exists
        if (!$booking->vehicle) {
            if ($verbose) {
                $this->warn("   ⚠️  Vehicle not found - skipping");
            }
            return 'skipped';
        }

        if ($verbose) {
            $this->info("   💰 Amount: AED {$booking->total_amount}");
            $this->info("   🚗 Vehicle: {$booking->vehicle->make} {$booking->vehicle->model}");
        }

        if ($dryRun) {
            if ($verbose) {
                $this->info("   🔍 DRY RUN: Would create invoice for booking {$booking->id}");
            }
            return 'success';
        }

        // Actually create invoice
        try {
            $invoice = $bookingInvoiceService->createInvoice($booking);
            
            if ($verbose) {
                $this->info("   ✅ Invoice created successfully: {$invoice->invoice_number}");
            }
            return 'success';
        } catch (\Exception $e) {
            if ($verbose) {
                $this->error("   ❌ Failed to create invoice: {$e->getMessage()}");
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
        $this->line("   Has Invoice: " . ($booking->bookingInvoices()->exists() ? 'Yes' : 'No'));
    }
}
