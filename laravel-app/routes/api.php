<?php

use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\PredictionApiController;
use App\Http\Controllers\Api\PropertyApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - House Price Predictor
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'House Price Predictor API',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Location & Locality Endpoints
Route::get('/locations', [LocationApiController::class, 'getLocations']);
Route::get('/localities', [LocationApiController::class, 'getLocalities']);
Route::get('/amenities', [LocationApiController::class, 'getAmenities']);

// Property Listings & Filtering Endpoints
Route::get('/properties', [PropertyApiController::class, 'index']);
Route::get('/properties/{id}', [PropertyApiController::class, 'show']);

// Real-Estate Prediction API (Section 15, rate limited)
Route::post('/predict', [PredictionApiController::class, 'predict'])->middleware('throttle:60,1');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
