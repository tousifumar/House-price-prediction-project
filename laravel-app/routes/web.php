<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketAnalyticsController;
use App\Http\Controllers\PredictionWebController;
use App\Http\Controllers\PriceAlertController;
use App\Http\Controllers\PropertyCompareController;
use App\Http\Controllers\SavedPropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - House Price Predictor
|--------------------------------------------------------------------------
*/

// Public Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/market-analytics', [MarketAnalyticsController::class, 'index'])->name('market-analytics');
Route::get('/compare', [PropertyCompareController::class, 'index'])->name('compare');

use App\Http\Controllers\Admin\AdminAmenityController;
use App\Http\Controllers\Admin\AdminDatasetController;
use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\Admin\AdminModelController;
use App\Http\Controllers\Admin\AdminPropertyController;
use App\Http\Controllers\Admin\AdminUserController;

// Dedicated Property Price Prediction Form (Accessible by both guests and users, rate-limited)
Route::get('/predict', [PredictionWebController::class, 'create'])->name('predict');
Route::post('/predict', [PredictionWebController::class, 'store'])->middleware('throttle:60,1')->name('predict.store');
Route::get('/predictions/{id}', [PredictionWebController::class, 'show'])->name('predictions.show');
Route::get('/predictions/{id}/pdf', [PredictionWebController::class, 'downloadPdf'])->name('predictions.pdf');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Quick 1-Click Demo Login
Route::post('/auth/demo-login', [AuthController::class, 'demoLogin'])->name('auth.demo-login');

// Authenticated User Routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Prediction History
    Route::get('/predictions', [PredictionWebController::class, 'index'])->name('predictions.index');
    Route::delete('/predictions/{id}', [PredictionWebController::class, 'destroy'])->name('predictions.destroy');

    // Saved Properties
    Route::get('/saved-properties', [SavedPropertyController::class, 'index'])->name('saved-properties.index');
    Route::post('/saved-properties', [SavedPropertyController::class, 'store'])->name('saved-properties.store');
    Route::delete('/saved-properties/{id}', [SavedPropertyController::class, 'destroy'])->name('saved-properties.destroy');

    // Price Alerts
    Route::get('/price-alerts', [PriceAlertController::class, 'index'])->name('price-alerts.index');
    Route::post('/price-alerts', [PriceAlertController::class, 'store'])->name('price-alerts.store');
    Route::patch('/price-alerts/{id}/toggle', [PriceAlertController::class, 'toggle'])->name('price-alerts.toggle');
    Route::delete('/price-alerts/{id}', [PriceAlertController::class, 'destroy'])->name('price-alerts.destroy');
});

// Admin Command Center Routes
Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{id}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::patch('/users/{id}/status', [AdminUserController::class, 'toggleStatus'])->name('users.status');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Property Management
    Route::get('/properties', [AdminPropertyController::class, 'index'])->name('properties.index');
    Route::post('/properties', [AdminPropertyController::class, 'store'])->name('properties.store');
    Route::put('/properties/{id}', [AdminPropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{id}', [AdminPropertyController::class, 'destroy'])->name('properties.destroy');

    // Location Management
    Route::get('/locations', [AdminLocationController::class, 'index'])->name('locations.index');
    Route::post('/locations/localities', [AdminLocationController::class, 'storeLocality'])->name('locations.localities.store');
    Route::put('/locations/localities/{id}', [AdminLocationController::class, 'updateLocality'])->name('locations.localities.update');
    Route::patch('/locations/localities/{id}/toggle', [AdminLocationController::class, 'toggleLocality'])->name('locations.localities.toggle');

    // Amenity Management
    Route::get('/amenities', [AdminAmenityController::class, 'index'])->name('amenities.index');
    Route::post('/amenities', [AdminAmenityController::class, 'store'])->name('amenities.store');
    Route::patch('/amenities/{id}/toggle', [AdminAmenityController::class, 'toggle'])->name('amenities.toggle');

    // Dataset Management
    Route::get('/datasets', [AdminDatasetController::class, 'index'])->name('datasets.index');
    Route::post('/datasets', [AdminDatasetController::class, 'store'])->name('datasets.store');
    Route::get('/datasets/{id}/preview', [AdminDatasetController::class, 'preview'])->name('datasets.preview');
    Route::delete('/datasets/{id}', [AdminDatasetController::class, 'destroy'])->name('datasets.destroy');

    // ML Model Management & Retraining
    Route::get('/models', [AdminModelController::class, 'index'])->name('models.index');
    Route::patch('/models/{id}/activate', [AdminModelController::class, 'activate'])->name('models.activate');
    Route::post('/models/train', [AdminModelController::class, 'train'])->name('models.train');
});
