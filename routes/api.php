<?php
// routes/api.php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Shipment API Documentation",
 *     description="API documentation for Shipment Management System",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PaketController;
use App\Http\Controllers\API\ShipmentController;
use App\Http\Controllers\API\TrackingController;
use App\Http\Controllers\API\AreaPengirimanController;
use App\Http\Controllers\API\PriceController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KategoriBarangController;
use App\Http\Controllers\API\BarangController;

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
    Route::apiResource('paket', PaketController::class);

    // Shipment routes
    Route::apiResource('shipments', ShipmentController::class);

    // Tracking routes (hanya untuk tambah update)
    Route::post('tracking', [TrackingController::class, 'addUpdate']);

    // Area Pengiriman routes
    Route::apiResource('area-pengiriman', AreaPengirimanController::class);

    // Price routes
    Route::apiResource('prices', PriceController::class);
    Route::post('calculate-shipping', [PriceController::class, 'calculateShippingCost']);

    // Kategori Barang routes
    Route::apiResource('kategori-barang', KategoriBarangController::class);

    // Barang routes
    Route::apiResource('barang', BarangController::class);
});