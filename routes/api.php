<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PointSysController;
use App\Http\Controllers\VehiclesApiController;
use App\Models\Vehicle;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

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

});
