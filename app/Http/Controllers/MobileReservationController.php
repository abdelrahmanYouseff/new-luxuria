<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\User;
use App\Services\ExternalBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MobileReservationController extends Controller
{
    protected $externalBookingService;

    public function __construct(ExternalBookingService $externalBookingService)
    {
        $this->externalBookingService = $externalBookingService;
    }

    /**
     * Create automatic pending reservation in RLAPP
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createReservation(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'vehicle_id' => 'required|exists:vehicles,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date',
                'emirate' => 'required|string|in:Dubai,Abu Dhabi,Sharjah,Ajman,Umm Al Quwain,Ras Al Khaimah,Fujairah',
                'notes' => 'nullable|string|max:500',
                'pickup_location' => 'nullable|string|max:255',
                'dropoff_location' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get authenticated user
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }

            // Get vehicle
            $vehicle = Vehicle::findOrFail($request->vehicle_id);

            // Check vehicle availability
            if (strtolower($vehicle->status) !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'المركبة غير متاحة للحجز حالياً',
                    'vehicle_status' => $vehicle->status
                ], 400);
            }

            // Check for booking conflicts
            if (Booking::hasConflict($request->vehicle_id, $request->start_date, $request->end_date)) {
                return response()->json([
                    'success' => false,
                    'message' => 'المركبة محجوزة في التواريخ المحددة'
                ], 400);
            }

            // Calculate pricing
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $totalDays = $startDate->diffInDays($endDate);

            if ($totalDays === 0) {
                $totalDays = 1; // Minimum 1 day
            }

            // Determine pricing type and rate
            $pricingType = 'daily';
            $appliedRate = (float) $vehicle->daily_rate;

            if ($totalDays >= 7 && $totalDays < 28 && (float) $vehicle->weekly_rate > 0) {
                $pricingType = 'weekly';
                $appliedRate = (float) $vehicle->weekly_rate / 7;
            } elseif ($totalDays >= 28 && (float) $vehicle->monthly_rate > 0) {
                $pricingType = 'monthly';
                $appliedRate = round(((float) $vehicle->monthly_rate) / 30, 2);
            }

            if ($appliedRate <= 0) {
                $appliedRate = max((float) $vehicle->daily_rate, 1);
            }

            $totalAmount = round($totalDays * $appliedRate, 2);

            // Prepare booking data for RLAPP
            $bookingData = [
                'emirate' => $request->emirate,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'daily_rate' => $vehicle->daily_rate,
                'pricing_type' => $pricingType,
                'applied_rate' => $appliedRate,
                'total_days' => $totalDays,
                'total_amount' => $totalAmount,
                'notes' => $request->notes ?? '',
                'pickup_location' => $request->pickup_location ?? $request->emirate,
                'dropoff_location' => $request->dropoff_location ?? $request->emirate
            ];

            Log::info('Mobile app creating reservation in RLAPP', [
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'booking_data' => $bookingData
            ]);

            // Create reservation in RLAPP first
            $rlappResult = $this->externalBookingService->createExternalBooking(
                $bookingData,
                $user->id,
                $vehicle->id
            );

            if (!$rlappResult['success']) {
                Log::error('Failed to create reservation in RLAPP', [
                    'user_id' => $user->id,
                    'vehicle_id' => $vehicle->id,
                    'error' => $rlappResult['message']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'فشل في إنشاء الحجز في النظام الخارجي',
                    'external_error' => $rlappResult['message']
                ], 500);
            }

            // Create local booking record
            $localBooking = Booking::create([
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'emirate' => $request->emirate,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'daily_rate' => $vehicle->daily_rate,
                'pricing_type' => $pricingType,
                'applied_rate' => $appliedRate,
                'total_days' => $totalDays,
                'total_amount' => $totalAmount,
                'notes' => $request->notes ?? '',
                'status' => 'pending',
                'external_reservation_id' => $rlappResult['external_booking_id'] ?? null,
                'external_reservation_uid' => $rlappResult['external_booking_uid'] ?? null,
            ]);

            Log::info('Mobile reservation created successfully', [
                'local_booking_id' => $localBooking->id,
                'external_reservation_id' => $localBooking->external_reservation_id,
                'external_reservation_uid' => $localBooking->external_reservation_uid
            ]);

            // Return success response with booking details
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحجز بنجاح',
                'data' => [
                    'reservation' => [
                        'id' => $localBooking->id,
                        'external_reservation_id' => $localBooking->external_reservation_id,
                        'external_reservation_uid' => $localBooking->external_reservation_uid,
                        'status' => $localBooking->status,
                        'vehicle' => [
                            'id' => $vehicle->id,
                            'make' => $vehicle->make,
                            'model' => $vehicle->model,
                            'year' => $vehicle->year,
                            'plate_number' => $vehicle->plate_number
                        ],
                        'dates' => [
                            'start_date' => $localBooking->start_date,
                            'end_date' => $localBooking->end_date,
                            'total_days' => $localBooking->total_days
                        ],
                        'pricing' => [
                            'pricing_type' => $localBooking->pricing_type,
                            'applied_rate' => $localBooking->applied_rate,
                            'total_amount' => $localBooking->total_amount
                        ],
                        'location' => [
                            'emirate' => $localBooking->emirate,
                            'pickup_location' => $request->pickup_location ?? $request->emirate,
                            'dropoff_location' => $request->dropoff_location ?? $request->emirate
                        ],
                        'notes' => $localBooking->notes,
                        'created_at' => $localBooking->created_at->toISOString()
                    ],
                    'rlapp_response' => $rlappResult
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Mobile reservation creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الحجز',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's reservations for mobile app
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserReservations(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }

            $reservations = Booking::with('vehicle')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'external_reservation_id' => $booking->external_reservation_id,
                        'external_reservation_uid' => $booking->external_reservation_uid,
                        'status' => $booking->status,
                        'vehicle' => [
                            'id' => $booking->vehicle->id,
                            'make' => $booking->vehicle->make,
                            'model' => $booking->vehicle->model,
                            'year' => $booking->vehicle->year,
                            'plate_number' => $booking->vehicle->plate_number,
                            'image_url' => $booking->vehicle->image_url
                        ],
                        'dates' => [
                            'start_date' => $booking->start_date,
                            'end_date' => $booking->end_date,
                            'total_days' => $booking->total_days
                        ],
                        'pricing' => [
                            'pricing_type' => $booking->pricing_type,
                            'applied_rate' => $booking->applied_rate,
                            'total_amount' => $booking->total_amount
                        ],
                        'location' => [
                            'emirate' => $booking->emirate
                        ],
                        'notes' => $booking->notes,
                        'created_at' => $booking->created_at->toISOString()
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'تم جلب الحجوزات بنجاح',
                'data' => [
                    'reservations' => $reservations,
                    'total_count' => $reservations->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get user reservations', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب الحجوزات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a reservation
     *
     * @param Request $request
     * @param int $reservationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelReservation(Request $request, $reservationId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً'
                ], 401);
            }

            $booking = Booking::where('id', $reservationId)
                ->where('user_id', $user->id)
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحجز غير موجود'
                ], 404);
            }

            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'الحجز ملغي مسبقاً'
                ], 400);
            }

            // Update status in RLAPP if external reservation exists
            if ($booking->external_reservation_id || $booking->external_reservation_uid) {
                $identifier = $booking->external_reservation_uid ?? $booking->external_reservation_id;
                $isUid = !empty($booking->external_reservation_uid);

                $rlappResult = $this->externalBookingService->updateExternalBookingStatus(
                    $identifier,
                    'cancelled',
                    $isUid
                );

                Log::info('RLAPP cancellation result', [
                    'booking_id' => $booking->id,
                    'external_id' => $identifier,
                    'rlapp_result' => $rlappResult
                ]);
            }

            // Update local booking status
            $booking->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الحجز بنجاح',
                'data' => [
                    'reservation_id' => $booking->id,
                    'status' => $booking->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cancel reservation', [
                'user_id' => Auth::id(),
                'reservation_id' => $reservationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في إلغاء الحجز',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
