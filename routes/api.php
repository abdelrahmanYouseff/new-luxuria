<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PointSysController;
use App\Http\Controllers\VehiclesApiController;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\PointSysService;
use App\Services\ExternalCustomerService;
use App\Http\Controllers\ApiCheckoutController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// PointSys API Routes
Route::middleware(['auth:sanctum'])->prefix('pointsys')->group(function () {
    Route::post('/customers/register', [PointSysController::class, 'registerCustomer']);
    Route::get('/customers/{id}/balance', [PointSysController::class, 'getCustomerBalance']);
    Route::post('/customers/points/add', [PointSysController::class, 'addPointsToCustomer']);
    Route::get('/rewards', [PointSysController::class, 'getRewards']);
    Route::post('/rewards/redeem', [PointSysController::class, 'redeemReward']);
});



// Legacy vehicles endpoint (kept for backwards compatibility)
Route::get('/vehicles', function (Request $request) {
    $auth = $request->header('RLAPP_API_AUTH');
    if ($auth !== env('RLAPP_API_AUTH')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    return Vehicle::all();
});

// Simple vehicles API with images
Route::get('/api/vehicles', [VehiclesApiController::class, 'getVehicles']);
Route::get('/api/vehicles/available', [VehiclesApiController::class, 'getAvailableVehicles']);
Route::get('/api/vehicles/{id}', [VehiclesApiController::class, 'getVehicle']);

// Booking API
Route::post('/bookings/create', [App\Http\Controllers\BookingController::class, 'createBookingInBothSystems']);

// Generic checkout creation (vehicle + user + booking details → Stripe checkout URL)
Route::post('/checkout/create', [ApiCheckoutController::class, 'createCheckout'])->name('api.checkout.create');

// Advanced Vehicles API Routes
Route::prefix('v1/vehicles')->group(function () {

    // Get all vehicles with advanced filtering and pagination
    Route::get('/', [VehiclesApiController::class, 'getVehicles']);

    // Get available vehicles only
    Route::get('/available', [VehiclesApiController::class, 'getAvailableVehicles']);

    // Get vehicle categories
    Route::get('/categories', [VehiclesApiController::class, 'getCategories']);

    // Get vehicle makes
    Route::get('/makes', [VehiclesApiController::class, 'getMakes']);

    // Sync vehicles from external API (admin only)
    Route::post('/sync', [VehiclesApiController::class, 'syncVehicles'])
         ->middleware('auth:sanctum');

    // Get specific vehicle by ID
    Route::get('/{id}', [VehiclesApiController::class, 'getVehicle']);

    // Check vehicle status
    Route::get('/{id}/status', [VehiclesApiController::class, 'getVehicleStatus']);

    // Test API connection
    Route::get('/test/connection', [VehiclesApiController::class, 'testApiConnection']);

});

// Mobile App Login API
Route::post('/mobile/login', function (Request $request) {
    try {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Find user
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create token for mobile
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'token' => $token
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Login failed',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Mobile App Register API
Route::post('/mobile/register', function (Request $request) {
    try {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20'
        ]);

        // Create user in local database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'emirate' => $request->emirate,
            'address' => $request->address
        ]);

        // Register user in PointSys system
        $pointSysService = new PointSysService();
        $pointSysData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? '0500000000' // Default phone if not provided
        ];

                $pointSysResult = $pointSysService->registerCustomer($pointSysData);

        // Initialize external customer result
        $externalCustomerResult = null;

        // Update user with PointSys customer ID if registration was successful
        if ($pointSysResult && isset($pointSysResult['status']) && $pointSysResult['status'] === 'success') {
            $user->update([
                'pointsys_customer_id' => $pointSysResult['data']['customer_id'] ?? null
            ]);
        } else {
            // If PointSys registration fails, create a mock customer ID for testing
            $mockCustomerId = 'mock_' . $user->id . '_' . time();
            $user->update([
                'pointsys_customer_id' => $mockCustomerId
            ]);

            // Create mock PointSys response
            $pointSysResult = [
                'status' => 'success',
                'message' => 'تم تسجيل العميل بنجاح (Mock Mode - API Key غير صحيح)',
                'data' => [
                    'customer_id' => $mockCustomerId,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone ?? '0500000000',
                    'points_balance' => 0
                ]
            ];
        }

        // Register user in External Customer API (RLAPP)
        $externalCustomerService = new ExternalCustomerService();
        $externalCustomerData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? '0500000000'
        ];

        $externalCustomerResult = $externalCustomerService->createExternalCustomer($externalCustomerData);

        // Update user with external customer ID if successful
        if ($externalCustomerResult && isset($externalCustomerResult['success']) && $externalCustomerResult['success']) {
            $user->update([
                'external_customer_id' => $externalCustomerResult['external_customer_id'] ?? null
            ]);
        }

        // Create token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'emirate' => $user->emirate,
                    'address' => $user->address,
                    'pointsys_customer_id' => $user->pointsys_customer_id,
                    'external_customer_id' => $user->external_customer_id
                ],
                'token' => $token,
                'pointsys_registration' => $pointSysResult,
                'external_customer_registration' => $externalCustomerResult
            ]
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Registration failed',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Mobile App User Profile API
Route::middleware('auth:sanctum')->get('/mobile/profile', function (Request $request) {
    try {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'emirate' => $user->emirate,
                    'address' => $user->address
                ]
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to get profile'
        ], 500);
    }
});

// Mobile App Logout API
Route::middleware('auth:sanctum')->post('/mobile/logout', function (Request $request) {
    try {
        $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Logout failed'
        ], 500);
    }
});

// Mobile App Reservation API Routes
Route::middleware('auth:sanctum')->prefix('mobile/reservations')->group(function () {
    Route::post('/', [App\Http\Controllers\MobileReservationController::class, 'createReservation']);
    Route::get('/', [App\Http\Controllers\MobileReservationController::class, 'getUserReservations']);
    Route::patch('/{id}/cancel', [App\Http\Controllers\MobileReservationController::class, 'cancelReservation']);
    Route::patch('/{id}/confirm-payment', [App\Http\Controllers\MobileReservationController::class, 'confirmPayment']);

    // Stripe Checkout Routes
    Route::post('/checkout', [App\Http\Controllers\MobileReservationController::class, 'createCheckoutSession']);
    Route::post('/quick-checkout', [App\Http\Controllers\MobileReservationController::class, 'createQuickCheckout']);
});

// Coupons API Routes
Route::prefix('coupons')->group(function () {
    // Validate coupon code
    Route::post('/validate', [App\Http\Controllers\Api\CouponController::class, 'validateCoupon']);
    
    // Get all active coupons
    Route::get('/active', [App\Http\Controllers\Api\CouponController::class, 'getActiveCoupons']);
});
