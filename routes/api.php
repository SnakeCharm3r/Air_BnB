<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InfrastructureController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth.api')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/auth/profile', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/password', [AuthController::class, 'updatePassword']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // User profile (used by frontend /api/user)
    Route::get('/user', [AuthController::class, 'me']);

    // Users management
    Route::apiResource('users', UserController::class);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/notifications', [DashboardController::class, 'notifications']);

    // Rooms
    Route::get('/rooms/available', [RoomController::class, 'available']);
    Route::apiResource('rooms', RoomController::class);

    // Bookings
    Route::get('/bookings/today/checkins', [BookingController::class, 'todayCheckIns']);
    Route::get('/bookings/today/checkouts', [BookingController::class, 'todayCheckOuts']);
    Route::post('/bookings/{booking}/checkin', [BookingController::class, 'checkIn']);
    Route::post('/bookings/{booking}/checkout', [BookingController::class, 'checkOut']);
    Route::apiResource('bookings', BookingController::class);

    // Inventory
    Route::apiResource('inventory', InventoryController::class);

    // Tasks
    Route::apiResource('tasks', TaskController::class);

    // Operational costs
    Route::apiResource('costs', CostController::class);

    // Maintenance
    Route::apiResource('maintenance', MaintenanceController::class);

    // Infrastructure
    Route::apiResource('infrastructure', InfrastructureController::class);
});
