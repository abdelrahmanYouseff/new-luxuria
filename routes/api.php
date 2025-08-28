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

// Mobile App User Registration API
Route::post('/mobile/users/register', function (Request $request) {
    try {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'emirate' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500'
        ]);

        // Create user in local database
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'emirate' => $request->emirate ?? 'Dubai',
            'address' => $request->address ?? 'Mobile App User',
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'user',
        ]);

        // Register user in PointSys system with original data first, then search for existing customer
        $pointSysService = new \App\Services\PointSysService();
        $pointSysResult = null;
        $pointSysRegistered = false;
        $attempts = 0;
        $maxAttempts = 2;
        $mobileStartTime = microtime(true);

        while (!$pointSysRegistered && $attempts < $maxAttempts) {
            // Check if we've been running for more than 12 seconds total
            if ((microtime(true) - $mobileStartTime) > 12) {
                \Log::warning('Mobile PointSys registration timeout after 12 seconds', [
                    'user_id' => $user->id,
                    'attempts' => $attempts
                ]);
                break;
            }

            try {
                $pointSysData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone ?? '050' . rand(1000000, 9999999)
                ];

                // First attempt: Use original data
                if ($attempts === 0) {
                    \Log::info('Mobile: Attempting PointSys registration with original data', [
                        'user_id' => $user->id,
                        'email' => $pointSysData['email'],
                        'phone' => $pointSysData['phone'],
                        'attempt' => $attempts + 1
                    ]);
                }

                // Second attempt: If email exists, try to find and link existing customer
                if ($attempts === 1) {
                    \Log::info('Mobile: Email exists in PointSys, searching for existing customer', [
                        'user_id' => $user->id,
                        'email' => $pointSysData['email']
                    ]);

                    // Search for existing customer (simplified version)
                    $foundCustomerId = findExistingPointSysCustomer($pointSysData['email']);

                    if ($foundCustomerId) {
                        // Link to existing customer
                        $user->update([
                            'pointsys_customer_id' => $foundCustomerId
                        ]);

                        \Log::info('Mobile: Linked to existing PointSys customer', [
                            'user_id' => $user->id,
                            'pointsys_customer_id' => $foundCustomerId,
                            'email' => $pointSysData['email']
                        ]);

                        $pointSysResult = [
                            'status' => 'success',
                            'message' => 'تم ربط العميل بالعميل الموجود في PointSys',
                            'data' => [
                                'customer_id' => $foundCustomerId,
                                'name' => $request->name,
                                'email' => $pointSysData['email'],
                                'phone' => $pointSysData['phone'],
                                'points_balance' => 0
                            ]
                        ];

                        $pointSysRegistered = true;
                        break;
                    } else {
                        // Create with modified email as last resort
                        $emailParts = explode('@', $pointSysData['email']);
                        $pointSysData['email'] = $emailParts[0] . '_' . time() . '_' . $attempts . '@' . $emailParts[1];
                        $pointSysData['phone'] = $pointSysData['phone'] . '_' . time() . '_' . $attempts;

                        \Log::info('Mobile: Creating new customer with modified email', [
                            'user_id' => $user->id,
                            'original_email' => $request->email,
                            'modified_email' => $pointSysData['email'],
                            'attempt' => $attempts + 1
                        ]);
                    }
                }

                $apiResult = $pointSysService->registerCustomer($pointSysData);

                if ($apiResult && isset($apiResult['data']['customer_id'])) {
                    // Success! Update local database
                    $user->update([
                        'pointsys_customer_id' => $apiResult['data']['customer_id']
                    ]);

                    \Log::info('Mobile: Customer registered in PointSys successfully', [
                        'user_id' => $user->id,
                        'pointsys_customer_id' => $apiResult['data']['customer_id'],
                        'attempt' => $attempts + 1,
                        'email_used' => $pointSysData['email'],
                        'is_original_data' => $attempts === 0
                    ]);

                    $pointSysResult = $apiResult;
                    $pointSysRegistered = true;
                } else {
                    // Check if it's an email already exists error
                    if ($apiResult && isset($apiResult['errors']['email'])) {
                        \Log::warning('Mobile: Email already exists in PointSys', [
                            'user_id' => $user->id,
                            'email' => $pointSysData['email'],
                            'attempt' => $attempts + 1
                        ]);
                    } else {
                        \Log::warning('Mobile: Failed to register customer in PointSys', [
                            'user_id' => $user->id,
                            'response' => $apiResult,
                            'attempt' => $attempts + 1,
                            'data_sent' => $pointSysData
                        ]);
                    }
                    $attempts++;
                }
            } catch (\Exception $e) {
                \Log::error('Mobile: Exception while registering customer in PointSys', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'attempt' => $attempts + 1
                ]);
                $attempts++;
            }
        }

        // If all attempts failed, create mock data
        if (!$pointSysRegistered) {
            \Log::warning('Mobile: Failed to register customer in PointSys after all attempts', [
                'user_id' => $user->id,
                'attempts' => $attempts
            ]);

            // Create mock customer ID for testing
            $mockCustomerId = 'mock_' . $user->id . '_' . time();
            $user->update([
                'pointsys_customer_id' => $mockCustomerId
            ]);

            $pointSysResult = [
                'status' => 'success',
                'message' => 'تم تسجيل العميل بنجاح (Mock Mode - PointSys غير متاح)',
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
        $externalCustomerService = new \App\Services\ExternalCustomerService();
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

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل المستخدم بنجاح',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'emirate' => $user->emirate,
                    'address' => $user->address,
                    'role' => $user->role,
                    'pointsys_customer_id' => $user->pointsys_customer_id,
                    'external_customer_id' => $user->external_customer_id
                ],
                'token' => $token,
                'pointsys_registration' => $pointSysResult,
                'external_registration' => $externalCustomerResult
            ]
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'بيانات غير صحيحة',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Mobile user registration failed', [
            'error' => $e->getMessage(),
            'data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'فشل في تسجيل المستخدم',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Mobile App Register API (Legacy) - This route is deprecated, use /mobile/users/register instead
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

        // Register user in PointSys system with original data first, then search for existing customer
        $pointSysResult = null;
        $pointSysRegistered = false;
        $attempts = 0;
        $maxAttempts = 2;
        $mobileStartTime = microtime(true);

        while (!$pointSysRegistered && $attempts < $maxAttempts) {
            // Check if we've been running for more than 12 seconds total
            if ((microtime(true) - $mobileStartTime) > 12) {
                \Log::warning('Mobile PointSys registration timeout after 12 seconds', [
                    'user_id' => $user->id,
                    'attempts' => $attempts
                ]);
                break;
            }
            try {
                $pointSysData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone ?? '0500000000'
                ];

                // First attempt: Use original data
                if ($attempts === 0) {
                    \Log::info('Mobile: Attempting PointSys registration with original data', [
                        'user_id' => $user->id,
                        'email' => $pointSysData['email'],
                        'phone' => $pointSysData['phone'],
                        'attempt' => $attempts + 1
                    ]);
                }

                // Second attempt: If email exists, try to find and link existing customer
                if ($attempts === 1) {
                    \Log::info('Mobile: Email exists in PointSys, searching for existing customer', [
                        'user_id' => $user->id,
                        'email' => $pointSysData['email']
                    ]);

                    // Search for existing customer (simplified version)
                    $foundCustomerId = $this->findExistingPointSysCustomer($pointSysData['email']);

                    if ($foundCustomerId) {
                        // Link to existing customer
                        $user->update([
                            'pointsys_customer_id' => $foundCustomerId
                        ]);

                        \Log::info('Mobile: Linked to existing PointSys customer', [
                            'user_id' => $user->id,
                            'pointsys_customer_id' => $foundCustomerId,
                            'email' => $pointSysData['email']
                        ]);

                        $pointSysResult = [
                            'status' => 'success',
                            'message' => 'تم ربط العميل بالعميل الموجود في PointSys',
                            'data' => [
                                'customer_id' => $foundCustomerId,
                                'name' => $request->name,
                                'email' => $pointSysData['email'],
                                'phone' => $pointSysData['phone'],
                                'points_balance' => 0
                            ]
                        ];

                        $pointSysRegistered = true;
                        break;
                    } else {
                        // Create with modified email as last resort
                        $emailParts = explode('@', $pointSysData['email']);
                        $pointSysData['email'] = $emailParts[0] . '_' . time() . '_' . $attempts . '@' . $emailParts[1];
                        $pointSysData['phone'] = $pointSysData['phone'] . '_' . time() . '_' . $attempts;

                        \Log::info('Mobile: Creating new customer with modified email', [
                            'user_id' => $user->id,
                            'original_email' => $request->email,
                            'modified_email' => $pointSysData['email'],
                            'attempt' => $attempts + 1
                        ]);
                    }
                }

                $apiResult = $pointSysService->registerCustomer($pointSysData);

                if ($apiResult && isset($apiResult['data']['customer_id'])) {
                    // Success! Update local database
                    $user->update([
                        'pointsys_customer_id' => $apiResult['data']['customer_id']
                    ]);

                    \Log::info('Mobile: Customer registered in PointSys successfully', [
                        'user_id' => $user->id,
                        'pointsys_customer_id' => $apiResult['data']['customer_id'],
                        'attempt' => $attempts + 1,
                        'email_used' => $pointSysData['email'],
                        'is_original_data' => $attempts === 0
                    ]);

                    $pointSysResult = $apiResult;
                    $pointSysRegistered = true;
                } else {
                    // Check if it's an email already exists error
                    if ($apiResult && isset($apiResult['errors']['email'])) {
                        \Log::warning('Mobile: Email already exists in PointSys', [
                            'user_id' => $user->id,
                            'email' => $pointSysData['email'],
                            'attempt' => $attempts + 1
                        ]);
                    } else {
                        \Log::warning('Mobile: Failed to register customer in PointSys', [
                            'user_id' => $user->id,
                            'response' => $apiResult,
                            'attempt' => $attempts + 1,
                            'data_sent' => $pointSysData
                        ]);
                    }
                    $attempts++;
                }
            } catch (\Exception $e) {
                \Log::error('Mobile: Exception while registering customer in PointSys', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'attempt' => $attempts + 1
                ]);
                $attempts++;
            }
        }

        // If all attempts failed, create mock data
        if (!$pointSysRegistered) {
            \Log::warning('Mobile: Failed to register customer in PointSys after all attempts', [
                'user_id' => $user->id,
                'attempts' => $attempts
            ]);

            // Create mock customer ID for testing
            $mockCustomerId = 'mock_' . $user->id . '_' . time();
            $user->update([
                'pointsys_customer_id' => $mockCustomerId
            ]);

            $pointSysResult = [
                'status' => 'success',
                'message' => 'تم تسجيل العميل بنجاح (Mock Mode - PointSys غير متاح)',
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

// Mobile App User Points API
Route::middleware('auth:sanctum')->get('/mobile/points', [App\Http\Controllers\MobileReservationController::class, 'getUserPoints']);
Route::middleware('auth:sanctum')->get('/mobile/points/booking-stats', [App\Http\Controllers\MobileReservationController::class, 'getBookingPointsStats']);
Route::middleware('auth:sanctum')->get('/mobile/points/booking-history', [App\Http\Controllers\MobileReservationController::class, 'getBookingPointsHistory']);

// Mobile App Coupons API
Route::middleware('auth:sanctum')->get('/mobile/coupons', [App\Http\Controllers\MobileReservationController::class, 'getMobileCoupons']);
Route::middleware('auth:sanctum')->post('/mobile/coupons/validate', [App\Http\Controllers\MobileReservationController::class, 'validateCouponCode']);
Route::middleware('auth:sanctum')->post('/mobile/coupons/checkout', [App\Http\Controllers\MobileReservationController::class, 'createCouponCheckout']);

// Temporary route for testing mobile coupons without auth
Route::get('/mobile/coupons/test', [App\Http\Controllers\MobileReservationController::class, 'getMobileCoupons']);
Route::post('/mobile/coupons/test-checkout', [App\Http\Controllers\MobileReservationController::class, 'createCouponCheckout']);

// Coupons API Routes
Route::prefix('coupons')->group(function () {
    // Validate coupon code
    Route::post('/validate', [App\Http\Controllers\Api\CouponController::class, 'validateCoupon']);

    // Get all active coupons
    Route::get('/active', [App\Http\Controllers\Api\CouponController::class, 'getActiveCoupons']);
});
