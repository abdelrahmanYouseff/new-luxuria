<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use App\Models\Car;
use App\Models\Vehicle;



// Public routes (no middleware)
Route::get('/coupons', [App\Http\Controllers\CouponController::class, 'index'])->name('coupons.index');
Route::post('/coupons', [App\Http\Controllers\CouponController::class, 'store'])->name('coupons.store')->withoutMiddleware(['web']);

// API endpoint for frontend coupons
Route::get('/api/coupons', [App\Http\Controllers\CouponController::class, 'getCouponsApi'])->name('api.coupons.index')->withoutMiddleware(['web']);



Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Public vehicles route (no authentication required)
Route::get('/vehicles-public', [App\Http\Controllers\VehicleController::class, 'index'])->name('vehicles.public');

// Public vehicles route - accessible without login (shows only visible vehicles)
Route::get('/vehicles', [App\Http\Controllers\VehicleController::class, 'publicIndex'])->name('vehicles.index.public');

// Test page to explain vehicle routes
Route::get('/test-vehicle-routes', function () {
    return view('test_vehicle_routes');
})->name('test.vehicle.routes');

// Vehicle visibility management (admin only)
Route::middleware(['auth'])->group(function () {
    Route::patch('/vehicles/{vehicle}/toggle-visibility', [App\Http\Controllers\VehicleController::class, 'toggleVisibility'])->name('vehicles.toggle-visibility');
});

// Debug route to test authentication
Route::get('/debug-auth', function () {
    return response()->json([
        'authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user' => \Illuminate\Support\Facades\Auth::user() ? [
            'id' => \Illuminate\Support\Facades\Auth::user()->id,
            'name' => \Illuminate\Support\Facades\Auth::user()->name,
            'email' => \Illuminate\Support\Facades\Auth::user()->email,
            'role' => \Illuminate\Support\Facades\Auth::user()->role
        ] : null,
        'csrf_token' => csrf_token()
    ]);
})->middleware(['auth'])->name('debug.auth');

// Temporary test dashboard route
Route::get('/test-dashboard', function () {
    return Inertia::render('Dashboard', [
        'reservationsCount' => 0,
        'invoicesCount' => 0,
        'couponsCount' => 0,
        'vehicalsCount' => 0,
    ]);
})->middleware(['auth'])->name('test.dashboard');

// Public vehicles route for testing
Route::get('/vehicles-public', [App\Http\Controllers\VehicleController::class, 'index'])->name('vehicles.public');

// Inertia Dashboard (for Vue.js)
Route::get('dashboard', function () {
    return Inertia::render('Dashboard', [
        'reservationsCount' => Reservation::count(),
        'invoicesCount' => Transaction::count(),
        'couponsCount' => Coupon::count(),
        'vehicalsCount' => Vehicle::count(),
    ]);
})->middleware(['auth', 'check.session'])->name('dashboard');



Route::get('/cars/{id}', function ($id) {
    $vehicle = App\Models\Vehicle::findOrFail($id);
    // Siblings of same make+model
    $siblings = App\Models\Vehicle::where('make', $vehicle->make)
        ->where('model', $vehicle->model)
        ->get();

    // Prefer an Available unit to represent/pricing this model on details page
    $effectiveVehicle = $siblings->first(function ($v) {
        return strtolower($v->status) === 'available';
    }) ?? $vehicle;

    // Effective rates come from the effective vehicle; if missing fall back to first non-zero among siblings, then to current vehicle
    $daily = (float) ($effectiveVehicle->daily_rate ?? 0);
    $weekly = (float) ($effectiveVehicle->weekly_rate ?? 0);
    $monthly = (float) ($effectiveVehicle->monthly_rate ?? 0);

    if ($daily <= 0) {
        $daily = (float) (optional($siblings)->pluck('daily_rate')->filter(fn($v) => (float) $v > 0)->first() ?? ($vehicle->daily_rate ?? 0));
    }
    if ($weekly <= 0) {
        $weekly = (float) (optional($siblings)->pluck('weekly_rate')->filter(fn($v) => (float) $v > 0)->first() ?? ($vehicle->weekly_rate ?? 0));
    }
    if ($monthly <= 0) {
        $monthly = (float) (optional($siblings)->pluck('monthly_rate')->filter(fn($v) => (float) $v > 0)->first() ?? ($vehicle->monthly_rate ?? 0));
    }

    $effectiveRates = [
        'daily' => $daily,
        'weekly' => $weekly,
        'monthly' => $monthly,
    ];

    return view('cars.show', [
        'vehicle' => $vehicle,
        'effectiveVehicle' => $effectiveVehicle,
        'effectiveRates' => $effectiveRates,
    ]);
})->name('cars.show');

// Booking routes
Route::middleware(['auth'])->group(function () {
    Route::get('/vehicles/{vehicle}/availability', [App\Http\Controllers\BookingController::class, 'checkAvailability'])->name('vehicles.availability');
    Route::get('/vehicles/{vehicle}/alternatives', [App\Http\Controllers\BookingController::class, 'getAlternativeVehicles'])->name('vehicles.alternatives');
    Route::post('/bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/booking/summary/{vehicle}', [App\Http\Controllers\BookingController::class, 'showSummary'])->name('booking.summary');
    Route::post('/booking/confirm', [App\Http\Controllers\BookingController::class, 'confirm'])->name('booking.confirm');
    Route::get('/my-bookings', [App\Http\Controllers\BookingController::class, 'getUserBookings'])->name('bookings.my');
    Route::patch('/bookings/{booking}/cancel', [App\Http\Controllers\BookingController::class, 'cancel'])->name('bookings.cancel');

    // My bookings page
    Route::get('/my-bookings-page', function () {
        return view('bookings.my-bookings');
    })->name('bookings.page');

    // Booking payment routes
    Route::post('/booking/payment', [App\Http\Controllers\StripeController::class, 'createBookingPaymentSession'])->name('booking.payment');
    Route::get('/booking/payment/success', [App\Http\Controllers\StripeController::class, 'handleBookingPaymentSuccess'])->name('booking.payment.success');

    // Mock payment page for testing
    Route::get('/booking/mock-payment/{booking}', function ($bookingId) {
        $booking = App\Models\Booking::with('vehicle')->findOrFail($bookingId);
        return view('booking-mock-payment', compact('booking'));
    })->name('booking.mock.payment');
});

// Test external booking API (public route for testing)
Route::get('/test-external-booking', function () {
    $externalBookingService = app(\App\Services\ExternalBookingService::class);

    $testData = [
        'emirate' => 'Dubai',
        'start_date' => '2025-07-27',
        'end_date' => '2025-08-06',
        'applied_rate' => 1099.86,
        'notes' => 'Test booking from Luxuria UAE'
    ];

    $result = $externalBookingService->createExternalBooking($testData, 1, 31);

    return response()->json($result);
})->name('test.external.booking');

// Test external customer API (public route for testing)
Route::get('/test-external-customer', function () {
    $externalCustomerService = app(\App\Services\ExternalCustomerService::class);

    $testData = [
        'name' => 'Test User',
        'email' => 'test' . time() . '@example.com',
        'phone' => '0501234567'
    ];

    $result = $externalCustomerService->createExternalCustomer($testData);

    return response()->json($result);
})->name('test.external.customer');

// Test external customer registration page
Route::get('/test-external-registration', function () {
    return view('test_external_registration');
})->name('test.external.registration');

// Test booking payment confirmation page
Route::get('/test-booking-payment', function () {
    return view('test_booking_payment');
})->name('test.booking.payment');

// Test real PointSys API
Route::get('/test-realtime-pointsys', function () {
    try {
        $pointSysService = app(\App\Services\PointSysService::class);

        // Test with a known customer ID from database
        $customerId = '019821eb-37b2-dbf3-6c04-8dcd83933d2d';

        $balance = $pointSysService->getCustomerBalance($customerId);

        return response()->json([
            'success' => true,
            'test_customer_id' => $customerId,
            'pointsys_response' => $balance,
            'api_key_exists' => !empty(config('services.pointsys.api_key')),
            'base_url' => config('services.pointsys.base_url'),
            'use_mock' => false
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('test.realtime.pointsys');

// Register user in PointSys API
Route::get('/register-user-pointsys', function () {
    try {
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated'
            ], 401);
        }

        $pointSysService = app(\App\Services\PointSysService::class);

        // Register user in PointSys
        $registrationData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '0501234567'
        ];

        $registrationResult = $pointSysService->registerCustomer($registrationData);

        if ($registrationResult && isset($registrationResult['data']['customer_id'])) {
            // Update user's pointsys_customer_id in database
            $user->pointsys_customer_id = $registrationResult['data']['customer_id'];
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully in PointSys',
                'customer_id' => $registrationResult['data']['customer_id'],
                'registration_response' => $registrationResult
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Failed to register user in PointSys',
                'registration_response' => $registrationResult
            ], 500);
        }

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->middleware(['auth'])->name('register.user.pointsys');

// Test PointSys registration API (without auth)
Route::get('/test-pointsys-registration', function () {
    try {
        $pointSysService = app(\App\Services\PointSysService::class);

        // Test registration with sample data
        $registrationData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0501234567'
        ];

        $registrationResult = $pointSysService->registerCustomer($registrationData);

        return response()->json([
            'success' => true,
            'registration_data' => $registrationData,
            'registration_response' => $registrationResult,
            'api_key_exists' => !empty(config('services.pointsys.api_key')),
            'base_url' => config('services.pointsys.base_url')
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('test.pointsys.registration');

// Test user points with auto-registration
Route::get('/test-user-points-auto-register', function () {
    try {
        $userPointsController = app(\App\Http\Controllers\UserPointsController::class);
        return $userPointsController->getCurrentUserPoints();
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->middleware(['auth'])->name('test.user.points.auto.register');







// Test booking payment success simulation
Route::post('/test-booking-payment-success', function (Request $request) {
    $bookingId = $request->input('booking_id');
    $booking = \App\Models\Booking::with('vehicle', 'user')->find($bookingId);

    if (!$booking) {
        return response()->json(['success' => false, 'message' => 'Booking not found']);
    }

    // Simulate payment success
    $booking->update(['status' => 'confirmed']);

    // Update external booking if exists
    $externalUpdateResult = null;
    if ($booking->external_reservation_id) {
        $externalBookingService = app(\App\Services\ExternalBookingService::class);
        $externalUpdateResult = $externalBookingService->updateExternalBookingStatus(
            $booking->external_reservation_id,
            'confirmed'
        );
    }

    return response()->json([
        'success' => true,
        'booking' => $booking,
        'external_update' => $externalUpdateResult
    ]);
})->name('test.booking.payment.success');

// Test external booking status update
Route::post('/test-external-booking-update', function (Request $request) {
    $externalBookingId = $request->input('external_booking_id', 'test-id');
    $status = $request->input('status', 'confirmed');

    $externalBookingService = app(\App\Services\ExternalBookingService::class);
    $result = $externalBookingService->updateExternalBookingStatus($externalBookingId, $status);

    // Add additional debug information
    $result['update_url'] = 'https://rlapp.rentluxuria.com/api/v1/reservations/' . $externalBookingId . '/status';
    $result['request_data'] = [
        'external_booking_id' => $externalBookingId,
        'status' => $status
    ];

    return response()->json($result);
})->name('test.external.booking.update');

// Test external booking update page
Route::get('/test-external-booking-update-page', function () {
    return view('test_external_booking_update');
})->name('test.external.booking.update.page');

// Debug route to check authentication and booking confirm
Route::get('/debug-booking-confirm', function () {
    return response()->json([
        'authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user' => \Illuminate\Support\Facades\Auth::user() ? [
            'id' => \Illuminate\Support\Facades\Auth::user()->id,
            'name' => \Illuminate\Support\Facades\Auth::user()->name,
            'email' => \Illuminate\Support\Facades\Auth::user()->email
        ] : null,
        'booking_confirm_route' => route('booking.confirm'),
        'csrf_token' => csrf_token()
    ]);
})->name('debug.booking.confirm');

// Test booking form page
Route::get('/test-booking-form', function () {
    return view('test_booking_form');
})->name('test.booking.form');

// Simple test route for booking confirm
Route::post('/test-booking-confirm', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Test route working!',
        'received_data' => $request->all(),
        'user_authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user_id' => \Illuminate\Support\Facades\Auth::id()
    ]);
})->name('test.booking.confirm');

// Test route without middleware
Route::post('/test-no-auth', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'No auth route working!',
        'received_data' => $request->all()
    ]);
})->name('test.no.auth');

// API endpoint to get bookings status
Route::get('/api/bookings/status', function () {
    $bookings = \App\Models\Booking::with('vehicle', 'user')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    return response()->json([
        'success' => true,
        'bookings' => $bookings
    ]);
})->name('api.bookings.status');

// API route for booking status updates
Route::patch('/api/bookings/{booking}/status', function (Request $request, $bookingId) {
    $booking = \App\Models\Booking::find($bookingId);

    if (!$booking) {
        return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
    }

    $booking->update(['status' => $request->status]);

    // Update external booking if exists
    if ($booking->external_reservation_id) {
        $externalBookingService = app(\App\Services\ExternalBookingService::class);
        $externalUpdateResult = $externalBookingService->updateExternalBookingStatus(
            $booking->external_reservation_id,
            $request->status
        );

        return response()->json([
            'success' => true,
            'local_booking' => $booking,
            'external_update' => $externalUpdateResult
        ]);
    }

    return response()->json(['success' => true, 'booking' => $booking]);
})->name('api.bookings.status');

// Share user role with all Inertia pages
Inertia::share('userRole', function () {
    return Auth::check() ? Auth::user()->role : null;
});

Route::middleware(['auth'])->group(function () {
    Route::get('/my-points', function () {
        return Inertia::render('MyPoints');
    })->name('my-points');

    Route::get('/vehicles-auth', [App\Http\Controllers\VehicleController::class, 'index'])->name('vehicles.index');
    Route::post('/vehicles/sync', [App\Http\Controllers\VehicleController::class, 'syncFromApi'])->name('vehicles.sync');
    Route::get('/vehicles-api', [App\Http\Controllers\VehiclesApiController::class, 'index'])->name('vehicles.api');
Route::get('/vehicles-sync', function () {
    return Inertia::render('VehiclesSync');
})->name('vehicles.sync.page');

    Route::get('/vehicles/{id}/image', function ($id) {
        return Inertia::render('VehicleImageManager', ['vehicleId' => $id]);
    })->name('vehicles.image');

    // Database Vehicles Routes
    Route::prefix('database/vehicles')->name('database.vehicles.')->group(function () {
        Route::get('/', [App\Http\Controllers\DatabaseVehicleController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\DatabaseVehicleController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\DatabaseVehicleController::class, 'store'])->name('store');
        Route::get('/{vehicle}', [App\Http\Controllers\DatabaseVehicleController::class, 'show'])->name('show');
        Route::get('/{vehicle}/edit', [App\Http\Controllers\DatabaseVehicleController::class, 'edit'])->name('edit');
        Route::put('/{vehicle}', [App\Http\Controllers\DatabaseVehicleController::class, 'update'])->name('update');
        Route::delete('/{vehicle}', [App\Http\Controllers\DatabaseVehicleController::class, 'destroy'])->name('destroy');
        Route::post('/sync', [App\Http\Controllers\DatabaseVehicleController::class, 'syncFromApi'])->name('sync');
        Route::get('/category/{category}', [App\Http\Controllers\DatabaseVehicleController::class, 'byCategory'])->name('byCategory');
        Route::get('/available', [App\Http\Controllers\DatabaseVehicleController::class, 'available'])->name('available');
    });
});

// Admin Routes (only for admin users)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::get('/vehicles', [App\Http\Controllers\AdminController::class, 'vehicles'])->name('vehicles');
    Route::get('/analytics', [App\Http\Controllers\AdminController::class, 'analytics'])->name('analytics');

    // Coupon Website Routes
    Route::get('/coupon-website', [App\Http\Controllers\Admin\CouponWebsiteController::class, 'index'])->name('coupon-website.index');
    Route::get('/coupon-website/create', [App\Http\Controllers\Admin\CouponWebsiteController::class, 'create'])->name('coupon-website.create');
    Route::post('/coupon-website', [App\Http\Controllers\Admin\CouponWebsiteController::class, 'store'])->name('coupon-website.store');
    Route::get('/coupon-website/{coupon}/edit', [App\Http\Controllers\Admin\CouponWebsiteController::class, 'edit'])->name('coupon-website.edit');
    Route::put('/coupon-website/{coupon}', [App\Http\Controllers\Admin\CouponWebsiteController::class, 'update'])->name('coupon-website.update');
    Route::delete('/coupon-website/{coupon}', [App\Http\Controllers\Admin\CouponWebsiteController::class, 'destroy'])->name('coupon-website.destroy');
});

// User Routes (for regular users)
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
    Route::get('/reservations', [App\Http\Controllers\UserController::class, 'reservations'])->name('reservations');
    Route::get('/invoices', [App\Http\Controllers\UserController::class, 'invoices'])->name('invoices');
});



// User Invoices Page (with sidebar and header) - removed to avoid conflict

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';



// Stripe Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/stripe/public-key', [App\Http\Controllers\StripeController::class, 'getPublicKey'])->name('stripe.public-key');
    Route::post('/stripe/create-payment-intent', [App\Http\Controllers\StripeController::class, 'createPaymentIntent'])->name('stripe.create-payment-intent');
    Route::post('/stripe/create-checkout-session', [App\Http\Controllers\StripeController::class, 'createCheckoutSession'])->name('stripe.create-checkout-session');
    Route::post('/stripe/payment-success', [App\Http\Controllers\StripeController::class, 'handlePaymentSuccess'])->name('stripe.payment-success');
    Route::get('/stripe/user-invoices', [App\Http\Controllers\StripeController::class, 'getUserInvoices'])->name('stripe.user-invoices');
});

// User Points API routes
Route::middleware(['auth'])->group(function () {
    Route::get('/user-points', [App\Http\Controllers\UserPointsController::class, 'getCurrentUserPoints'])->name('user.points');
Route::post('/user-points/add', [App\Http\Controllers\UserPointsController::class, 'addPoints'])->name('user.points.add');
Route::get('/user-points/booking-stats', [App\Http\Controllers\UserPointsController::class, 'getBookingPointsStats'])->name('user.points.booking-stats');
Route::get('/user-points/booking-history', [App\Http\Controllers\UserPointsController::class, 'getBookingHistory'])->name('user.points.booking-history');
Route::get('/booking-points', function () {
    return Inertia::render('BookingPoints');
})->middleware(['auth'])->name('booking.points');

    // PointSys API routes for frontend
    Route::get('/api/pointsys/rewards', [App\Http\Controllers\PointSysController::class, 'getRewards'])->name('pointsys.rewards');
    Route::post('/api/pointsys/rewards/redeem', [App\Http\Controllers\PointSysController::class, 'redeemReward'])->name('pointsys.redeem');
});

// Stripe Webhook (no auth required)
Route::post('/stripe/webhook', [App\Http\Controllers\StripeController::class, 'handleWebhook'])->name('stripe.webhook');

Route::get('/stripe/success', [App\Http\Controllers\StripeController::class, 'handleSuccess'])->name('stripe.success');

// Mock payment page for testing
Route::get('/mock-payment', function () {
    $amount = request('amount', 0);
    $coupon_name = request('coupon_name', 'كوبون اختبار');
    $payment_intent_id = request('payment_intent_id', '');
    $coupon_id = request('coupon_id', '');

    return view('mock-payment', compact('amount', 'coupon_name', 'payment_intent_id', 'coupon_id'));
})->name('mock.payment');

// Test routes for PointSys integration
Route::get('/test-pointsys', function () {
    return view('test_pointsys');
});

Route::get('/test-pointsys-real', function () {
    return view('test_pointsys_real');
});

Route::get('/test-registration', function () {
    return view('test_registration');
});

Route::get('/test-real-registration', function () {
    return view('test_real_registration');
});

Route::get('/test-api-connection', function () {
    return view('test_api_connection');
});

Route::get('/test-user-points', function () {
    return view('test_user_points');
});

Route::get('/test-stripe-payment', function () {
    return view('test_stripe_payment');
});

Route::get('/test-simple-stripe', function () {
    return view('test_simple_stripe');
});

Route::get('/test-my-points', function () {
    return view('test_my_points');
});

Route::get('/test-rewards', function () {
    return view('test_rewards');
});

Route::get('/test-tier', function () {
    return view('test_tier');
});

Route::get('/test-mobile-reservations', function () {
    return view('test_mobile_reservations');
})->name('test.mobile.reservations');

Route::get('/test-vehicles', function () {
    return view('test_vehicles');
});

Route::get('/test-vehicles-table', function () {
    return view('test_vehicles_table');
});

Route::get('/test-vehicle-images', function () {
    return view('test_vehicle_images');
});

Route::get('/test-dropdown-actions', function () {
    return view('test_dropdown_actions');
});

Route::get('/test-vehicles-api', [App\Http\Controllers\VehicleController::class, 'testApi']);

Route::get('/test-api-vehicles', function () {
    return view('test_api_vehicles');
});

Route::get('/test-homepage', function () {
    return view('test_homepage');
});

Route::get('/test-vehicles-api-button', function () {
    return view('test_vehicles_api_button');
});

Route::get('/test-vehicles-api-page', function () {
    return view('test_vehicles_api_page');
});

Route::get('/test-vehicles-database', function () {
    return view('test_vehicles_database');
});

Route::get('/test-vehicles-database-updated', function () {
    return view('test_vehicles_database_updated');
});

// Test Image Upload Routes - REMOVED (TestImageUploadController does not exist)
// Route::get('/test-image-upload', [App\Http\Controllers\TestImageUploadController::class, 'index'])->name('test.image.upload');
// Route::post('/test-upload-image', [App\Http\Controllers\TestImageUploadController::class, 'upload'])->name('test.upload.image');
// Route::delete('/test-remove-image/{vehicle}', [App\Http\Controllers\TestImageUploadController::class, 'removeImage'])->name('test.remove.image');
// Route::get('/test-image-urls', [App\Http\Controllers\TestImageUploadController::class, 'testImageUrls'])->name('test.image.urls');

// Simple Image Test
Route::get('/simple-image-test', function () {
    $vehicles = App\Models\Vehicle::orderBy('make')->orderBy('model')->get();
    return view('simple_image_test', compact('vehicles'));
});
// Route::post('/simple-upload', [App\Http\Controllers\TestImageUploadController::class, 'upload']);

// Quick Image Test
Route::get('/quick-image-test', function () {
    $vehicles = App\Models\Vehicle::orderBy('make')->orderBy('model')->get();
    return view('quick_image_test', compact('vehicles'));
});

// Vehicle Image Management Routes
Route::get('/vehicles/{vehicle}/image', [App\Http\Controllers\VehicleImageController::class, 'show'])->name('vehicles.image.show');
Route::post('/vehicles/{vehicle}/image', [App\Http\Controllers\VehicleImageController::class, 'upload'])->name('vehicles.image.upload');
Route::delete('/vehicles/{vehicle}/image', [App\Http\Controllers\VehicleImageController::class, 'remove'])->name('vehicles.image.remove');

// Blade version for vehicle image management
Route::get('/vehicles/{vehicle}/image-blade', [App\Http\Controllers\VehicleImageController::class, 'showBlade'])->name('vehicles.image.show.blade');

// Test Vehicle Image System
Route::get('/test-vehicle-image-system', function () {
    $vehicles = App\Models\Vehicle::orderBy('make')->orderBy('model')->limit(12)->get();
    return view('test_vehicle_image_system', compact('vehicles'));
});

// Bulk Image Upload
Route::get('/bulk-image-upload', function () {
    $vehicles = App\Models\Vehicle::orderBy('make')->orderBy('model')->get();
    return view('bulk_image_upload', compact('vehicles'));
});

// Single Image Test
Route::get('/single-image-test', function () {
    $vehicles = App\Models\Vehicle::orderBy('make')->orderBy('model')->get();
    return view('single_image_test', compact('vehicles'));
});

// Quick Image Upload
Route::get('/quick-image-upload', function () {
    $vehicles = App\Models\Vehicle::orderBy('make')->orderBy('model')->get();
    return view('quick_image_upload', compact('vehicles'));
});

// Test Vehicles with Images
Route::get('/test-vehicles-images', function () {
    $vehicles = App\Models\Vehicle::orderBy('make')->orderBy('model')->get();
    return view('test_vehicles_images', compact('vehicles'));
});

// Test Vehicles Table with Year
Route::get('/test-vehicles-table-year', function () {
    $vehicles = App\Models\Vehicle::orderBy('daily_rate', 'desc')->get();
    return view('test_vehicles_table_year', compact('vehicles'));
});

Route::get('/user-invoices', function () {
    return view('user_invoices');
})->name('user.invoices.page');

Route::get('/test-invoices', function () {
    return view('test_invoices');
});

// Invoice Routes (temporarily without auth for testing)
Route::get('/invoice-coupons', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoice-coupons/api/user-invoices', [App\Http\Controllers\InvoiceController::class, 'getUserInvoices'])->name('invoices.user-invoices');
Route::get('/invoice-coupons/{id}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
Route::get('/invoice-coupons/{id}/download', [App\Http\Controllers\InvoiceController::class, 'download'])->name('invoices.download');
Route::get('/invoice-coupons/{id}/pdf', [App\Http\Controllers\InvoiceController::class, 'viewPdf'])->name('invoices.pdf');

// Invoice Routes (Smart route for both admin and users)
Route::get('/view-invoices', [App\Http\Controllers\InvoiceController::class, 'viewInvoices'])->middleware(['auth'])->name('invoices.view');

// Test login page
Route::get('/test-login', function () {
    return view('test-login');
})->name('test.login');

// صفحة about
Route::get('/about', function () {
    return view('about');
})->name('about');

// صفحة contact
Route::get('/contact', function () {
    return view('contact');
})->name('contact');


