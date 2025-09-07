<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\User;
use App\Services\ExternalBookingService;
use App\Services\BookingInvoiceService;
use App\Services\BookingPointsService;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmationMail;

class MobileController extends Controller
{
    /**
     * Mobile API Controller
     *
     * This controller handles all mobile API endpoints
     */

    private ExternalBookingService $externalBookingService;
    private BookingInvoiceService $bookingInvoiceService;
    private BookingPointsService $bookingPointsService;

    public function __construct(
        ExternalBookingService $externalBookingService,
        BookingInvoiceService $bookingInvoiceService,
        BookingPointsService $bookingPointsService
    ) {
        $this->externalBookingService = $externalBookingService;
        $this->bookingInvoiceService = $bookingInvoiceService;
        $this->bookingPointsService = $bookingPointsService;
    }

    /**
     * Create booking in rlapp system
     */
    public function createBooking(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'email' => 'required|email',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Find or create user by email
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name ?? 'Mobile User',
                'email' => $request->email,
                'password' => bcrypt('mobile_user_' . time()), // Generate temporary password
            ]
        );

        // Prepare booking data for RLAPP
        $bookingData = [
            'emirate' => $request->emirate ?? 'Dubai',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_rate' => $request->daily_rate ?? 0.00,
            'pricing_type' => 'daily',
            'applied_rate' => $request->applied_rate ?? 0.00,
            'total_days' => $request->total_days ?? 1,
            'total_amount' => $request->total_amount ?? 0,
            'notes' => $request->notes ?? '',
            'pickup_location' => $request->pickup_location ?? ($request->emirate ?? 'Dubai'),
            'dropoff_location' => $request->dropoff_location ?? ($request->emirate ?? 'Dubai')
        ];

        // Create booking in RLAPP first
        $rlappResult = $this->externalBookingService->createExternalBooking(
            $bookingData,
            $user->id,
            $request->vehicle_id
        );

        if (!$rlappResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking in external system',
                'external_error' => $rlappResult['message']
            ], 500);
        }

        // Create local booking with external IDs
        $booking = Booking::create([
            'user_id' => $user->id,
            'vehicle_id' => $request->vehicle_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'confirmed',
            'total_amount' => $request->total_amount ?? 0,
            'applied_rate' => $request->applied_rate ?? 0.00,
            'emirate' => $request->emirate ?? 'Dubai',
            'daily_rate' => $request->daily_rate ?? 0.00,
            'total_days' => $request->total_days ?? 1,
            'external_reservation_id' => $rlappResult['external_booking_id'] ?? null,
            'external_reservation_uid' => $rlappResult['external_booking_uid'] ?? null,
        ]);

        // Update booking status to confirmed in RLAPP system
        $rlappUpdateResult = ['success' => false, 'message' => 'No RLAPP update attempted'];

        if (!empty($rlappResult['external_booking_uid'])) {
            try {
                $rlappUpdateResult = $this->externalBookingService->updateBookingStatus(
                    $rlappResult['external_booking_uid'],
                    'confirmed'
                );
            } catch (\Exception $e) {
                $rlappUpdateResult = [
                    'success' => false,
                    'message' => 'Failed to update RLAPP status: ' . $e->getMessage()
                ];
            }
        } elseif (!empty($rlappResult['external_booking_id'])) {
            try {
                $rlappUpdateResult = $this->externalBookingService->updateBookingStatus(
                    $rlappResult['external_booking_id'],
                    'confirmed'
                );
            } catch (\Exception $e) {
                $rlappUpdateResult = [
                    'success' => false,
                    'message' => 'Failed to update RLAPP status: ' . $e->getMessage()
                ];
            }
        }

        // Create invoice for the booking
        $invoice = null;
        $invoiceResult = ['success' => false, 'message' => 'No invoice creation attempted'];

        try {
            $invoice = $this->bookingInvoiceService->createInvoice($booking);
            $invoiceResult = [
                'success' => true,
                'message' => 'Invoice created successfully',
                'invoice_number' => $invoice->invoice_number,
                'invoice_id' => $invoice->id
            ];
        } catch (\Exception $e) {
            $invoiceResult = [
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage()
            ];
        }

        // Add points to customer in PointSys
        $pointsResult = ['success' => false, 'message' => 'No points addition attempted'];

        try {
            $pointsResult = $this->bookingPointsService->addPointsToCustomer($booking);
        } catch (\Exception $e) {
            $pointsResult = [
                'success' => false,
                'message' => 'Failed to add points: ' . $e->getMessage()
            ];
        }

        // Send confirmation email with invoice PDF
        $emailResult = ['success' => false, 'message' => 'No email sending attempted'];

        try {
            Mail::to($user->email)->send(new BookingConfirmationMail($booking));
            $emailResult = [
                'success' => true,
                'message' => 'Confirmation email sent successfully with invoice PDF',
                'email_sent_to' => $user->email
            ];
        } catch (\Exception $e) {
            $emailResult = [
                'success' => false,
                'message' => 'Failed to send confirmation email: ' . $e->getMessage()
            ];
        
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully in both systems',
            'booking' => $booking,
            'external_booking_id' => $rlappResult['external_booking_id'] ?? null,
            'external_booking_uid' => $rlappResult['external_booking_uid'] ?? null,
            'rlapp_status_update' => $rlappUpdateResult,
            'invoice' => $invoice,
            'invoice_creation' => $invoiceResult,
            'points_addition' => $pointsResult,
            'email_confirmation' => $emailResult
        ], 201);
    }

    // Mobile API methods will be added here
}
