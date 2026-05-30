<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\RoomController;
use App\Http\Controllers\Web\RoomTypeController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\StaffController;
use App\Http\Controllers\Web\InventoryController;
use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Web\CostController;
use App\Http\Controllers\Web\MaintenanceController;
use App\Http\Controllers\Web\InfrastructureController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\BillingController;
use App\Http\Controllers\Web\ReportController;
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
    Route::get('/bookings/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/bookings/{id}/checkin', [BookingController::class, 'checkIn'])->name('bookings.checkin');
    Route::post('/bookings/{id}/checkout', [BookingController::class, 'checkOut'])->name('bookings.checkout');
    Route::post('/bookings/{id}/confirm', [BookingController::class, 'confirmBooking'])->name('bookings.confirm');
    Route::get('/bookings/{id}/invoice', [BookingController::class, 'invoice'])->name('bookings.invoice');
    Route::get('/bookings/{id}/invoice/print', [BookingController::class, 'printInvoice'])->name('bookings.invoice.print');
    
    // Billing
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/{bookingId}', [BillingController::class, 'show'])->name('billing.show');
    Route::post('/billing/{bookingId}/payment', [BillingController::class, 'processPayment'])->name('billing.payment');
    Route::post('/billing/{bookingId}/charge', [BillingController::class, 'addCharge'])->name('billing.charge');
    Route::get('/billing/{bookingId}/invoice', [BillingController::class, 'printInvoice'])->name('billing.invoice');
    
    // Staff
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{id}', [StaffController::class, 'show'])->name('staff.show');
    Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
    Route::get('/staff/{id}/attendance', [StaffController::class, 'attendance'])->name('staff.attendance');
    
    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    
    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
    Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::post('/inventory/{id}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    
    // Costs
    Route::get('/costs', [CostController::class, 'index'])->name('costs.index');
    Route::get('/costs/create', [CostController::class, 'create'])->name('costs.create');
    Route::post('/costs', [CostController::class, 'store'])->name('costs.store');
    Route::get('/costs/{id}', [CostController::class, 'show'])->name('costs.show');
    Route::get('/costs/{id}/edit', [CostController::class, 'edit'])->name('costs.edit');
    Route::put('/costs/{id}', [CostController::class, 'update'])->name('costs.update');
    Route::delete('/costs/{id}', [CostController::class, 'destroy'])->name('costs.destroy');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/costs', [ReportController::class, 'costs'])->name('reports.costs');
    Route::get('/reports/staff', [ReportController::class, 'staffPerformance'])->name('reports.staff');
    
    // Maintenance
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/{id}', [MaintenanceController::class, 'show'])->name('maintenance.show');
    Route::put('/maintenance/{id}', [MaintenanceController::class, 'update'])->name('maintenance.update');
    Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    
    // Infrastructure
    Route::get('/infrastructure', [InfrastructureController::class, 'index'])->name('infrastructure.index');
    Route::get('/infrastructure/create', [InfrastructureController::class, 'create'])->name('infrastructure.create');
    Route::post('/infrastructure', [InfrastructureController::class, 'store'])->name('infrastructure.store');
    Route::get('/infrastructure/{id}', [InfrastructureController::class, 'show'])->name('infrastructure.show');
    Route::get('/infrastructure/{id}/edit', [InfrastructureController::class, 'edit'])->name('infrastructure.edit');
    Route::put('/infrastructure/{id}', [InfrastructureController::class, 'update'])->name('infrastructure.update');
    Route::delete('/infrastructure/{id}', [InfrastructureController::class, 'destroy'])->name('infrastructure.destroy');
    Route::post('/infrastructure/{id}/toggle', [InfrastructureController::class, 'toggleStatus'])->name('infrastructure.toggle');
    
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/users/{user}/unlock', [SettingController::class, 'unlockUser'])->name('users.unlock');
});
