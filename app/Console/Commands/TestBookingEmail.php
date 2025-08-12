<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;

class TestBookingEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:booking-email {booking_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test booking confirmation email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookingId = $this->argument('booking_id');

        if ($bookingId) {
            $booking = Booking::with(['user', 'vehicle'])->find($bookingId);
            if (!$booking) {
                $this->error("Booking with ID {$bookingId} not found!");
                return 1;
            }
        } else {
            $booking = Booking::with(['user', 'vehicle'])->first();
            if (!$booking) {
                $this->error("No bookings found in database!");
                return 1;
            }
        }

        $this->info("Testing booking confirmation email for:");
        $this->info("Booking ID: {$booking->id}");
        $this->info("User: {$booking->user->name} ({$booking->user->email})");
        $this->info("Vehicle: {$booking->vehicle->make} {$booking->vehicle->model}");
        $this->info("Amount: AED {$booking->total_amount}");

        try {
            Mail::to($booking->user->email)->send(new BookingConfirmationMail($booking));
            $this->info("✅ Booking confirmation email sent successfully to {$booking->user->email}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to send booking confirmation email: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
