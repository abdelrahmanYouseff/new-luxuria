<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Services\ExternalBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $externalBookingService;

    public function __construct(ExternalBookingService $externalBookingService)
    {
        $this->externalBookingService = $externalBookingService;
    }

    /**
     * Check vehicle availability
     */
    public function checkAvailability($vehicleId, Request $request)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $isAvailable = true;
        $conflictingBookings = [];

        if ($startDate && $endDate) {
            $isAvailable = !Booking::hasConflict($vehicleId, $startDate, $endDate);

            if (!$isAvailable) {
                $conflictingBookings = Booking::where('vehicle_id', $vehicleId)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function ($q2) use ($startDate, $endDate) {
                              $q2->where('start_date', '<=', $startDate)
                                 ->where('end_date', '>=', $endDate);
                          });
                    })
                    ->with('user')
                    ->get();
            }
        }

        return response()->json([
            'available' => $isAvailable,
            'vehicle_status' => $vehicle->status,
            'conflicting_bookings' => $conflictingBookings,
            'vehicle' => [
                'id' => $vehicle->id,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'daily_rate' => $vehicle->daily_rate,
                'status' => $vehicle->status
            ]
        ]);
    }

    /**
     * Show booking summary page
     */
    public function showSummary(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        // Get booking data from request
        $bookingData = $request->all();

        // Ensure notes field exists
        $bookingData['notes'] = $bookingData['notes'] ?? '';

        // Validate required data
        $requiredFields = ['emirate', 'start_date', 'end_date', 'total_days', 'total_amount', 'pricing_type', 'applied_rate'];
        foreach ($requiredFields as $field) {
            if (!isset($bookingData[$field])) {
                return redirect()->route('cars.show', $vehicleId)->with('error', 'Invalid booking data. Please try again.');
            }
        }

        // Check for existing pending booking for this user/vehicle/dates
        $userId = Auth::id();
        $startDate = $bookingData['start_date'];
        $endDate = $bookingData['end_date'];

        $existingBooking = Booking::where('user_id', $userId)
            ->where('vehicle_id', $vehicleId)
            ->where('status', 'pending')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            })
            ->first();

        if ($existingBooking) {
            Log::info('Found existing pending booking, using it', [
                'booking_id' => $existingBooking->id
            ]);
            $booking = $existingBooking;
        } else {
            Log::info('No existing booking found, creating new pending booking');

            // Create external booking first
            $externalBookingResult = $this->externalBookingService->createExternalBooking(
                $bookingData,
                $userId,
                $vehicleId
            );

            // Cache external UID for later use (payment confirmation)
            if (!empty($externalBookingResult['external_booking_uid'])) {
                try {
                    \Cache::put('booking_uid_by_user_' . $userId, $externalBookingResult['external_booking_uid'], now()->addMinutes(15));
                    \Cache::put('booking_uid_by_session_' . session()->getId(), $externalBookingResult['external_booking_uid'], now()->addMinutes(15));
                } catch (\Throwable $e) {
                    \Log::warning('Failed to cache external booking UID', [
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Create local booking
            $booking = Booking::create([
                'user_id' => $userId,
                'vehicle_id' => $vehicleId,
                'emirate' => $bookingData['emirate'],
                'start_date' => $bookingData['start_date'],
                'end_date' => $bookingData['end_date'],
                'daily_rate' => $bookingData['daily_rate'],
                'pricing_type' => $bookingData['pricing_type'],
                'applied_rate' => $bookingData['applied_rate'],
                'total_days' => $bookingData['total_days'],
                'total_amount' => $bookingData['total_amount'],
                'notes' => $bookingData['notes'] ?? null,
                'status' => 'pending',
                'external_reservation_id' => $externalBookingResult['external_booking_id'] ?? null,
                'external_reservation_uid' => $externalBookingResult['external_booking_uid'] ?? null,
            ]);

            Log::info('New pending booking created', [
                'booking_id' => $booking->id,
                'external_reservation_id' => $booking->external_reservation_id,
                'external_reservation_uid' => $booking->external_reservation_uid
            ]);
        }

        return view('booking-summary', [
            'vehicle' => $vehicle,
            'bookingData' => $bookingData,
            'booking' => $booking
        ]);
    }

    /**
     * Confirm booking and process payment
     */
    public function confirm(Request $request)
    {
        Log::info('Booking confirm request received', [
            'payment_method' => $request->payment_method,
            'booking_id' => $request->booking_id,
            'booking_data' => $request->booking_data,
            'user_id' => Auth::id(),
            'user_authenticated' => Auth::check(),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'all_request_data' => $request->all()
        ]);

        // Check if we have an existing booking ID or need to create a new booking
        if ($request->has('booking_id')) {
            // Use existing booking
            $booking = Booking::where('id', $request->booking_id)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->firstOrFail();

            Log::info('Using existing booking', [
                'booking_id' => $booking->id
            ]);
        } else {
            // Create new booking (fallback)
            $request->validate([
                'booking_data' => 'required|json',
                'payment_method' => 'required|string',
            ]);

            Log::info('Validation passed, processing booking data');

            $bookingData = json_decode($request->booking_data, true);
            $vehicle = Vehicle::findOrFail($bookingData['vehicle_id']);

            Log::info('Vehicle found', [
                'vehicle_id' => $vehicle->id,
                'vehicle_status' => $vehicle->status
            ]);

            // Check if vehicle is still available
            if (strtolower($vehicle->status) !== 'available') {
                Log::warning('Vehicle not available', [
                    'vehicle_id' => $vehicle->id,
                    'vehicle_status' => $vehicle->status
                ]);
                return redirect()->back()->with('error', 'Vehicle is no longer available.');
            }

            // Check for date conflicts
            if (Booking::hasConflict($bookingData['vehicle_id'], $bookingData['start_date'], $bookingData['end_date'])) {
                Log::warning('Date conflict detected', [
                    'vehicle_id' => $bookingData['vehicle_id'],
                    'start_date' => $bookingData['start_date'],
                    'end_date' => $bookingData['end_date']
                ]);
                return redirect()->back()->with('error', 'Vehicle is already booked for the selected dates.');
            }

            Log::info('Checking for existing pending booking for this user/vehicle/dates');
            $userId = Auth::id();
            $vehicleId = $bookingData['vehicle_id'];
            $startDate = $bookingData['start_date'];
            $endDate = $bookingData['end_date'];

            $existingBooking = Booking::where('user_id', $userId)
                ->where('vehicle_id', $vehicleId)
                ->where('status', 'pending')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q2) use ($startDate, $endDate) {
                          $q2->where('start_date', '<=', $startDate)
                             ->where('end_date', '>=', $endDate);
                      });
                })
                ->first();

            if ($existingBooking) {
                Log::info('Found existing pending booking, will use it', [
                    'booking_id' => $existingBooking->id
                ]);
                $booking = $existingBooking;
            } else {
                Log::info('No existing booking found, creating new external booking');
                // Create external booking first
                $externalBookingResult = $this->externalBookingService->createExternalBooking(
                    $bookingData,
                    $userId,
                    $vehicleId
                );

                Log::info('External booking result', $externalBookingResult);

                Log::info('Creating local booking');

                // Create local booking
                $booking = Booking::create([
                    'user_id' => $userId,
                    'vehicle_id' => $vehicleId,
                    'emirate' => $bookingData['emirate'],
                    'start_date' => $bookingData['start_date'],
                    'end_date' => $bookingData['end_date'],
                    'daily_rate' => $bookingData['daily_rate'],
                    'pricing_type' => $bookingData['pricing_type'],
                    'applied_rate' => $bookingData['applied_rate'],
                    'total_days' => $bookingData['total_days'],
                    'total_amount' => $bookingData['total_amount'],
                    'notes' => $bookingData['notes'] ?? null,
                    'status' => 'pending',
                    'external_reservation_id' => $externalBookingResult['external_booking_id'] ?? null,
                    'external_reservation_uid' => $externalBookingResult['external_booking_uid'] ?? null,
                ]);

                Log::info('Local booking created', [
                    'booking_id' => $booking->id,
                    'external_reservation_id' => $booking->external_reservation_id
                ]);

                // Log external booking result
                if (!$externalBookingResult['success']) {
                    Log::warning('External booking failed but local booking created', [
                        'local_booking_id' => $booking->id,
                        'external_error' => $externalBookingResult['message']
                    ]);
                }
            }
        }

        // Process payment based on selected method
        if ($request->payment_method === 'stripe') {
            Log::info('Processing Stripe payment', [
                'booking_id' => $booking->id
            ]);

            // Create Stripe payment session using dependency injection
            $stripe = app(\App\Http\Controllers\StripeController::class);

            $paymentRequest = new \Illuminate\Http\Request();
            $paymentRequest->merge(['booking_id' => $booking->id]);

            $paymentResponse = $stripe->createBookingPaymentSession($paymentRequest);
            $paymentData = $paymentResponse->getData(true);

            Log::info('Stripe payment response', $paymentData);

            if ($paymentData['success']) {
                Log::info('Redirecting to Stripe payment', [
                    'payment_url' => $paymentData['payment_url']
                ]);
                return redirect($paymentData['payment_url']);
            } else {
                Log::error('Stripe payment failed, deleting booking', [
                    'booking_id' => $booking->id,
                    'error' => $paymentData['message'] ?? 'Unknown error'
                ]);
                $booking->delete();
                return redirect()->back()->with('error', 'Unable to create payment session. Please try again.');
            }
        } elseif ($request->payment_method === 'bank') {
            // Handle bank transfer
            return redirect()->route('booking.bank-transfer', $booking->id);
        } elseif ($request->payment_method === 'wallet') {
            // Handle digital wallet
            return redirect()->route('booking.wallet-payment', $booking->id);
        }

        Log::warning('Invalid payment method', [
            'payment_method' => $request->payment_method
        ]);
        return redirect()->back()->with('error', 'Invalid payment method selected.');
    }

    /**
     * Prepare booking data and show summary page
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'emirate' => 'required|string|max:100',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string|max:500',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Check if vehicle is available
        if (strtolower($vehicle->status) !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle is not available for booking.',
            ], 400);
        }

        // Check for date conflicts
        if (Booking::hasConflict($request->vehicle_id, $request->start_date, $request->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle is already booked for the selected dates.',
            ], 400);
        }

        // Calculate total days and amount with smart pricing
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1; // Include both start and end days

        // Apply smart pricing logic
        $pricingType = 'daily';
        $appliedRate = $vehicle->daily_rate;

        if ($totalDays <= 7) {
            // 1-7 days: Use daily rate
            $pricingType = 'daily';
            $appliedRate = $vehicle->daily_rate;
        } elseif ($totalDays > 7 && $totalDays < 28) {
            // 8-27 days: Use weekly rate divided by 7
            $pricingType = 'weekly';
            $appliedRate = $vehicle->weekly_rate / 7;
        } else {
            // 28+ days: Use monthly rate divided by 30
            $pricingType = 'monthly';
            $appliedRate = round($vehicle->monthly_rate / 30, 2);
        }

        $totalAmount = round($totalDays * $appliedRate, 2);

        // Prepare booking data for summary page
        $bookingData = [
            'vehicle_id' => $request->vehicle_id,
            'emirate' => $request->emirate,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_rate' => $vehicle->daily_rate,
            'pricing_type' => $pricingType,
            'applied_rate' => $appliedRate,
            'total_days' => $totalDays,
            'total_amount' => $totalAmount,
            'notes' => $request->notes ?? '',
        ];

        return response()->json([
            'success' => true,
            'message' => 'Redirecting to booking summary...',
            'redirect_url' => route('booking.summary', ['vehicle' => $vehicle->id]) . '?' . http_build_query($bookingData),
        ]);
    }

    /**
     * Get alternative vehicles when main vehicle is unavailable
     */
    public function getAlternativeVehicles($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        // Get alternative vehicles from same category, similar price, and available status
        $alternatives = Vehicle::whereRaw('LOWER(status) = ?', ['available'])
            ->where('category', $vehicle->category)
            ->where('id', '!=', $vehicleId)
            ->orderByRaw('ABS(daily_rate - ?)', [$vehicle->daily_rate])
            ->limit(3)
            ->get();

        // If we don't have 3 from same category, get from other categories but still available
        if ($alternatives->count() < 3) {
            $needed = 3 - $alternatives->count();
            $existingIds = $alternatives->pluck('id')->push($vehicleId)->toArray();

            $additionalAlternatives = Vehicle::whereRaw('LOWER(status) = ?', ['available'])
                ->whereNotIn('id', $existingIds)
                ->orderByRaw('ABS(daily_rate - ?)', [$vehicle->daily_rate])
                ->limit($needed)
                ->get();

            $alternatives = $alternatives->merge($additionalAlternatives);
        }

        return response()->json([
            'success' => true,
            'original_vehicle' => [
                'id' => $vehicle->id,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'year' => $vehicle->year,
                'status' => $vehicle->status,
                'daily_rate' => $vehicle->daily_rate,
                'category' => $vehicle->category,
                'image_url' => $vehicle->image_url,
            ],
            'alternatives' => $alternatives->map(function ($alt) use ($vehicle) {
                return [
                    'id' => $alt->id,
                    'make' => $alt->make,
                    'model' => $alt->model,
                    'year' => $alt->year,
                    'daily_rate' => $alt->daily_rate,
                    'weekly_rate' => $alt->weekly_rate,
                    'monthly_rate' => $alt->monthly_rate,
                    'category' => $alt->category,
                    'seats' => $alt->seats,
                    'doors' => $alt->doors,
                    'transmission' => $alt->transmission,
                    'color' => $alt->color,
                    'image_url' => $alt->image_url,
                    'price_difference' => $alt->daily_rate - $vehicle->daily_rate,
                    'price_difference_percent' => $vehicle->daily_rate > 0 ? round((($alt->daily_rate - $vehicle->daily_rate) / $vehicle->daily_rate) * 100, 1) : 0,
                ];
            }),
        ]);
    }

    /**
     * Get user's bookings
     */
    public function getUserBookings()
    {
        $bookings = Auth::user()->bookings()
            ->with('vehicle')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'bookings' => $bookings
        ]);
    }

    /**
     * Cancel a booking
     */
    public function cancel($bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be cancelled.',
            ], 400);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully.'
        ]);
    }

    /**
     * Create booking in both systems (RLAPP and local)
     */
    public function createBookingInBothSystems(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'user_id' => 'required|exists:users,id',
                'start_date' => 'required|date|after:today',
                'end_date' => 'required|date|after:start_date',
                'total_days' => 'required|integer|min:1',
                'total_amount' => 'required|numeric|min:0',
                'pricing_type' => 'required|in:daily,weekly,monthly',
                'applied_rate' => 'required|numeric|min:0',
                'emirate' => 'required|string',
                'notes' => 'nullable|string',
                'pickup_location' => 'nullable|string',
                'dropoff_location' => 'nullable|string'
            ]);

            // Get vehicle and user data
            $vehicle = Vehicle::findOrFail($request->vehicle_id);
            $user = \App\Models\User::findOrFail($request->user_id);

            // Check vehicle availability
            if (strtolower($vehicle->status) !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle is not available for booking',
                    'vehicle_status' => $vehicle->status
                ], 400);
            }

            // Check for booking conflicts
            if (Booking::hasConflict($request->vehicle_id, $request->start_date, $request->end_date)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle is not available for the selected dates'
                ], 400);
            }

            // Step 1: Create booking in RLAPP system
            $rlappBookingData = [
                'vehicle_id' => $vehicle->api_id,
                'customer_id' => $user->external_customer_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_days' => $request->total_days,
                'total_amount' => $request->total_amount,
                'pricing_type' => $request->pricing_type,
                'applied_rate' => $request->applied_rate,
                'emirate' => $request->emirate,
                'notes' => $request->notes,
                'pickup_location' => $request->pickup_location,
                'dropoff_location' => $request->dropoff_location
            ];

            $rlappResult = $this->externalBookingService->createExternalBooking($rlappBookingData, $request->user_id, $request->vehicle_id);

            if (!$rlappResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create booking in external system',
                    'external_error' => $rlappResult['message']
                ], 500);
            }

            // Step 2: Create booking in local system
            $localBookingData = [
                'user_id' => $request->user_id,
                'vehicle_id' => $request->vehicle_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_days' => $request->total_days,
                'total_amount' => $request->total_amount,
                'pricing_type' => $request->pricing_type,
                'applied_rate' => $request->applied_rate,
                'daily_rate' => $vehicle->daily_rate,
                'emirate' => $request->emirate,
                'notes' => $request->notes,
                'pickup_location' => $request->pickup_location,
                'dropoff_location' => $request->dropoff_location,
                'external_reservation_id' => $rlappResult['external_booking_id'] ?? null,
                'status' => 'confirmed'
            ];

            $localBooking = Booking::create($localBookingData);

            // Step 3: Update vehicle status to rented
            $vehicle->update(['status' => 'Rented']);

            // Step 4: Log the successful booking
            Log::info('Booking created successfully in both systems', [
                'local_booking_id' => $localBooking->id,
                'external_reservation_id' => $rlappResult['external_booking_id'] ?? null,
                'user_id' => $request->user_id,
                'vehicle_id' => $request->vehicle_id,
                'total_amount' => $request->total_amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully in both systems',
                'data' => [
                    'local_booking' => [
                        'id' => $localBooking->id,
                        'status' => $localBooking->status,
                        'total_amount' => $localBooking->total_amount,
                        'start_date' => $localBooking->start_date,
                        'end_date' => $localBooking->end_date
                    ],
                    'external_booking' => [
                        'reservation_id' => $rlappResult['external_booking_id'] ?? null,
                        'status' => $rlappResult['success'] ? 'created' : 'failed'
                    ],
                    'vehicle' => [
                        'id' => $vehicle->id,
                        'make' => $vehicle->make,
                        'model' => $vehicle->model,
                        'status' => $vehicle->status
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create booking in both systems: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
