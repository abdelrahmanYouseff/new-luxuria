<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BookingPointsService
{
    private PointSysService $pointSysService;

    // عدد النقاط لكل يوم إيجار
    private const POINTS_PER_DAY = 5;

    public function __construct(PointSysService $pointSysService)
    {
        $this->pointSysService = $pointSysService;
    }

    /**
     * حساب النقاط المكتسبة من الحجز
     */
    public function calculatePointsForBooking(Booking $booking): int
    {
        return $booking->total_days * self::POINTS_PER_DAY;
    }

    /**
     * إضافة النقاط للعميل عند نجاح الدفع
     */
    public function addPointsToCustomer(Booking $booking): array
    {
        try {
            // التحقق من أن النقاط لم تُضف من قبل
            if ($booking->points_added_to_customer) {
                Log::info('Points already added for booking', [
                    'booking_id' => $booking->id,
                    'points_earned' => $booking->points_earned
                ]);

                return [
                    'success' => true,
                    'message' => 'النقاط مضافة مسبقاً',
                    'points_added' => $booking->points_earned
                ];
            }

            // حساب النقاط
            $pointsToAdd = $this->calculatePointsForBooking($booking);

            if ($pointsToAdd <= 0) {
                Log::warning('No points to add for booking', [
                    'booking_id' => $booking->id,
                    'total_days' => $booking->total_days
                ]);

                return [
                    'success' => false,
                    'message' => 'لا توجد نقاط لإضافتها',
                    'points_added' => 0
                ];
            }

            // التحقق من وجود العميل في نظام PointSys
            $user = $booking->user;
            if (!$user->pointsys_customer_id) {
                Log::warning('User not registered in PointSys', [
                    'user_id' => $user->id,
                    'booking_id' => $booking->id
                ]);

                return [
                    'success' => false,
                    'message' => 'العميل غير مسجل في نظام النقاط',
                    'points_added' => 0
                ];
            }

            // إضافة النقاط إلى نظام PointSys
            $description = "نقاط إيجار سيارة - {$booking->vehicle->make} {$booking->vehicle->model} - {$booking->total_days} يوم";
            $referenceId = "BOOKING_{$booking->id}";

            $result = $this->pointSysService->addPointsToCustomer(
                $user->pointsys_customer_id,
                $pointsToAdd,
                $description,
                $referenceId
            );

            if ($result && isset($result['status']) && $result['status'] === 'success') {
                // تحديث الحجز لتسجيل النقاط المضافة
                $booking->update([
                    'points_earned' => $pointsToAdd,
                    'points_added_to_customer' => true
                ]);

                Log::info('Points added successfully to customer', [
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'pointsys_customer_id' => $user->pointsys_customer_id,
                    'points_added' => $pointsToAdd,
                    'total_days' => $booking->total_days,
                    'vehicle' => $booking->vehicle->make . ' ' . $booking->vehicle->model
                ]);

                return [
                    'success' => true,
                    'message' => "تم إضافة {$pointsToAdd} نقطة بنجاح",
                    'points_added' => $pointsToAdd,
                    'transaction_id' => $result['data']['transaction_id'] ?? null
                ];
            } else {
                Log::error('Failed to add points to PointSys', [
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'pointsys_customer_id' => $user->pointsys_customer_id,
                    'points_to_add' => $pointsToAdd,
                    'pointsys_response' => $result
                ]);

                return [
                    'success' => false,
                    'message' => 'فشل في إضافة النقاط إلى نظام PointSys',
                    'points_added' => 0,
                    'error' => $result['message'] ?? 'Unknown error'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception while adding points to customer', [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة النقاط',
                'points_added' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * الحصول على إجمالي النقاط المكتسبة من جميع الحجوزات
     */
    public function getTotalPointsEarned(User $user): int
    {
        return $user->bookings()
            ->where('points_added_to_customer', true)
            ->sum('points_earned');
    }

    /**
     * الحصول على إحصائيات النقاط للعميل
     */
    public function getCustomerPointsStats(User $user): array
    {
        $totalBookings = $user->bookings()->where('points_added_to_customer', true)->count();
        $totalPointsEarned = $this->getTotalPointsEarned($user);
        $totalDaysRented = $user->bookings()->where('points_added_to_customer', true)->sum('total_days');

        return [
            'total_bookings' => $totalBookings,
            'total_points_earned' => $totalPointsEarned,
            'total_days_rented' => $totalDaysRented,
            'points_per_day' => self::POINTS_PER_DAY
        ];
    }

    /**
     * الحصول على تاريخ النقاط من الحجوزات
     */
    public function getBookingHistory(User $user): array
    {
        $bookings = $user->bookings()
            ->with('vehicle')
            ->where('points_added_to_customer', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'booking_id' => $booking->id,
                    'vehicle' => [
                        'make' => $booking->vehicle->make,
                        'model' => $booking->vehicle->model,
                        'year' => $booking->vehicle->year,
                        'plate_number' => $booking->vehicle->plate_number
                    ],
                    'dates' => [
                        'start_date' => $booking->start_date,
                        'end_date' => $booking->end_date,
                        'total_days' => $booking->total_days
                    ],
                    'points' => [
                        'points_earned' => $booking->points_earned,
                        'points_per_day' => self::POINTS_PER_DAY
                    ],
                    'total_amount' => $booking->total_amount,
                    'created_at' => $booking->created_at->toISOString()
                ];
            });

        return [
            'bookings' => $bookings,
            'total_count' => $bookings->count()
        ];
    }
}
