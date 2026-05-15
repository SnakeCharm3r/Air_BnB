<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\RoomController;
use App\Http\Controllers\Web\RoomTypeController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes - Pure Laravel MVC (No API)
|--------------------------------------------------------------------------
*/

// Guest routes
Route::get('/', [DashboardController::class, 'index'])->middleware('auth');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes - Session-based auth
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rooms
    Route::get('/rooms', [RoomController::class, 'index'])->name('web.rooms');
    Route::post('/rooms', [RoomController::class, 'store'])->name('web.rooms.store');
    Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('web.rooms.show');
    Route::put('/rooms/{id}', [RoomController::class, 'update'])->name('web.rooms.update');
    
    // Room Types
    Route::get('/room-types', [RoomTypeController::class, 'index'])->name('room-types.index');
    Route::post('/room-types', [RoomTypeController::class, 'store'])->name('room-types.store');
    Route::put('/room-types/{id}', [RoomTypeController::class, 'update'])->name('room-types.update');
    Route::delete('/room-types/{id}', [RoomTypeController::class, 'destroy'])->name('room-types.destroy');
    
    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    
    // Billing
    Route::get('/billing', function () {
        return view('billing.index');
    })->name('billing');
    
    // Staff
    Route::get('/staff', function () {
        return view('staff.index');
    })->name('staff');
    Route::get('/staff/{id}', function ($id) {
        return view('staff.show', compact('id'));
    })->name('staff.show');
    
    // Tasks
    Route::get('/tasks', function () {
        return view('tasks.index');
    })->name('tasks');
    
    // Inventory
    Route::get('/inventory', function () {
        return view('inventory.index');
    })->name('inventory');
    
    // Costs
    Route::get('/costs', function () {
        return view('costs.index');
    })->name('costs');
    
    // Reports
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports');
    
    // Maintenance
    Route::get('/maintenance', function () {
        return view('maintenance.index');
    })->name('maintenance');
    
    // Infrastructure
    Route::get('/infrastructure', function () {
        return view('infrastructure.index');
    })->name('infrastructure');
    
    // User Management
    Route::get('/users', function () {
        return view('users.index');
    })->name('users');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/users/{user}/unlock', [SettingController::class, 'unlockUser'])->name('users.unlock');
    Route::post('/users/{user}/toggle-status', [SettingController::class, 'toggleUserStatus'])->name('users.toggle-status');
});
