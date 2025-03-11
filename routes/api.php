<?php
// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PackageController;
use App\Http\Controllers\API\ShipmentController;
use App\Http\Controllers\API\TrackingController;
use App\Http\Controllers\API\DeliveryZoneController;
use App\Http\Controllers\API\PriceController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Routes publik (tidak memerlukan autentikasi)
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('tracking/{trackingNumber}', [TrackingController::class, 'trackPackage']);
Route::get('track/{trackingNumber}', [PackageController::class, 'track']);

// Routes yang memerlukan autentikasi
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    // User routes
    Route::apiResource('users', UserController::class);

    // Package routes
    Route::apiResource('packages', PackageController::class);

    // Shipment routes
    Route::apiResource('shipments', ShipmentController::class);

    // Tracking routes (hanya untuk tambah update)
    Route::post('tracking', [TrackingController::class, 'addUpdate']);

    // Delivery Zone routes
    Route::apiResource('zones', DeliveryZoneController::class);

    // Price routes
    Route::apiResource('prices', PriceController::class);
    Route::post('calculate-shipping', [PriceController::class, 'calculateShippingCost']);
});