<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PointSysController;
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
Route::prefix('pointsys')->group(function () {
    Route::post('/customers/register', [PointSysController::class, 'registerCustomer']);
    Route::get('/customers/{id}/balance', [PointSysController::class, 'getCustomerBalance']);
    Route::post('/customers/points/add', [PointSysController::class, 'addPointsToCustomer']);
    Route::get('/rewards', [PointSysController::class, 'getRewards']);
    Route::post('/rewards/redeem', [PointSysController::class, 'redeemReward']);
});

Route::get('/vehicles', function (Request $request) {
    $auth = $request->header('RLAPP_API_AUTH');
    if ($auth !== env('RLAPP_API_AUTH')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    return Vehicle::all();
});
