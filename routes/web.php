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
use App\Http\Controllers\Web\FolioController;
use App\Http\Controllers\Web\ChargeController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\InvoiceController;
use App\Http\Controllers\Web\ReceiptController;
use App\Http\Controllers\Web\AccountingController;
use App\Http\Controllers\Web\AccountingExportController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\FinanceController;
use App\Http\Controllers\Web\MenuController;
use App\Http\Controllers\Web\KitchenOrderController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Web\PermissionController;

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
    
    // Room Types (admin/manager only)
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/room-types', [RoomTypeController::class, 'index'])->name('room-types.index');
        Route::post('/room-types', [RoomTypeController::class, 'store'])->name('room-types.store');
        Route::put('/room-types/{id}', [RoomTypeController::class, 'update'])->name('room-types.update');
        Route::delete('/room-types/{id}', [RoomTypeController::class, 'destroy'])->name('room-types.destroy');
    });

    // Bookings (receptionist/manager/admin)
    Route::middleware(['role:receptionist'])->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings');
        Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
        Route::get('/bookings/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
        Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
        Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
        Route::post('/bookings/{id}/checkin', [BookingController::class, 'checkIn'])->name('bookings.checkin');
        Route::post('/bookings/{id}/checkout', [BookingController::class, 'checkOut'])->name('bookings.checkout');
        Route::post('/bookings/{id}/extend-stay', [BookingController::class, 'extendStay'])->name('bookings.extend-stay');
        Route::post('/bookings/{id}/checkout-overdue', [BookingController::class, 'checkoutOverdue'])->name('bookings.checkout-overdue');
        Route::post('/bookings/{id}/confirm', [BookingController::class, 'confirmBooking'])->name('bookings.confirm');
        Route::post('/bookings/{id}/no-show', [BookingController::class, 'markNoShow'])->name('bookings.no-show');
        Route::get('/bookings/{id}/invoice', [BookingController::class, 'invoice'])->name('bookings.invoice');
        Route::get('/bookings/{id}/invoice/print', [BookingController::class, 'printInvoice'])->name('bookings.invoice.print');
    });

    // Accounting dashboard (receptionist/manager/admin)
    Route::middleware(['role:receptionist'])->group(function () {
        Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    });

    // Billing (receptionist/manager/admin)
    Route::middleware(['role:receptionist'])->group(function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/export', [AccountingExportController::class, 'billing'])->name('billing.export');
        Route::get('/billing/{bookingId}', [BillingController::class, 'show'])->name('billing.show');
        Route::post('/billing/{bookingId}/payment', [BillingController::class, 'processPayment'])->name('billing.payment');
        Route::post('/billing/{bookingId}/charge', [BillingController::class, 'addCharge'])->name('billing.charge');
        Route::get('/billing/{bookingId}/invoice', [BillingController::class, 'printInvoice'])->name('billing.invoice');
    });

    // Folio management (receptionist/manager/admin)
    Route::middleware(['role:receptionist'])->group(function () {
        Route::get('/folios', [FolioController::class, 'index'])->name('folios.index');
        Route::get('/folios/{folio}', [FolioController::class, 'show'])->name('folios.show');
        Route::get('/folios/{folio}/dashboard', [FolioController::class, 'dashboard'])->name('folios.dashboard');
        Route::post('/folios/{folio}/close', [FolioController::class, 'close'])->name('folios.close');
        Route::post('/folios/{folio}/void', [FolioController::class, 'void'])->name('folios.void');
    });

    // Charge posting (receptionist/manager/admin)
    Route::middleware(['role:receptionist'])->group(function () {
        Route::get('/charges', [ChargeController::class, 'index'])->name('charges.index');
        Route::get('/charges/create', [ChargeController::class, 'create'])->name('charges.create');
        Route::post('/charges', [ChargeController::class, 'store'])->name('charges.store');
        Route::post('/charges/{charge}/reverse', [ChargeController::class, 'reverse'])->name('charges.reverse');
    });

    // Payments (receptionist/manager/admin)
    Route::middleware(['role:receptionist'])->group(function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/export', [AccountingExportController::class, 'payments'])->name('payments.export');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
        Route::post('/payments/{payment}/void', [PaymentController::class, 'void'])->name('payments.void');
    });

    // Invoices (receptionist/manager/admin)
    Route::middleware(['role:receptionist'])->group(function () {
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/export', [AccountingExportController::class, 'invoices'])->name('invoices.export');
        Route::post('/invoices/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::post('/invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
        Route::post('/invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])->name('invoices.paid');
        Route::post('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::post('/invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');
    });

    // Receipts (receptionist/manager/admin)
    Route::middleware(['role:receptionist'])->group(function () {
        Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
        Route::get('/receipts/export', [AccountingExportController::class, 'receipts'])->name('receipts.export');
        Route::get('/receipts/{payment}', [ReceiptController::class, 'show'])->name('receipts.show');
        Route::get('/receipts/{payment}/print', [ReceiptController::class, 'print'])->name('receipts.print');
        Route::post('/receipts/{payment}/generate', [ReceiptController::class, 'generate'])->name('receipts.generate');
    });

    // Checkout (receptionist/manager/admin)
    Route::middleware(['role:receptionist'])->group(function () {
        Route::get('/checkout/{booking}', [CheckoutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout/{booking}', [CheckoutController::class, 'store'])->name('checkout.store');
    });

    // Finance dashboard (manager/admin)
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/finance', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
    });

    // Staff (admin/manager only)
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{id}', [StaffController::class, 'show'])->name('staff.show');
        Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::get('/staff/{id}/attendance', [StaffController::class, 'attendance'])->name('staff.attendance');
    });

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Inventory (receptionist and chef both)
    Route::middleware(['role:receptionist,chef'])->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
        Route::post('/inventory/{id}/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    });

    // Menu (receptionist and chef both)
    Route::middleware(['role:receptionist,chef'])->group(function () {
        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::get('/menu/{id}/edit', [MenuController::class, 'edit'])->name('menu.edit');
        Route::put('/menu/{id}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');
        Route::post('/menu/sync', [MenuController::class, 'syncFromIptv'])->name('menu.sync');
        Route::post('/menu/kitchen-hours', [MenuController::class, 'updateKitchenHours'])->name('menu.kitchen-hours');
    });

    // Kitchen Orders (receptionist and chef both)
    Route::middleware(['role:receptionist,chef'])->group(function () {
        Route::get('/kitchen-orders', [KitchenOrderController::class, 'index'])->name('kitchen-orders.index');
        Route::get('/kitchen-orders/create', [KitchenOrderController::class, 'create'])->name('kitchen-orders.create');
        Route::post('/kitchen-orders', [KitchenOrderController::class, 'store'])->name('kitchen-orders.store');
        Route::get('/kitchen-orders/{id}', [KitchenOrderController::class, 'show'])->name('kitchen-orders.show');
        Route::put('/kitchen-orders/{id}/status', [KitchenOrderController::class, 'updateStatus'])->name('kitchen-orders.status');
        Route::delete('/kitchen-orders/{id}', [KitchenOrderController::class, 'destroy'])->name('kitchen-orders.destroy');
    });

    // Costs (admin/manager only)
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/costs', [CostController::class, 'index'])->name('costs.index');
        Route::get('/costs/create', [CostController::class, 'create'])->name('costs.create');
        Route::post('/costs', [CostController::class, 'store'])->name('costs.store');
        Route::get('/costs/{id}', [CostController::class, 'show'])->name('costs.show');
        Route::get('/costs/{id}/edit', [CostController::class, 'edit'])->name('costs.edit');
        Route::put('/costs/{id}', [CostController::class, 'update'])->name('costs.update');
        Route::delete('/costs/{id}', [CostController::class, 'destroy'])->name('costs.destroy');
    });

    // Reports (admin/manager only)
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
        Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('/reports/costs', [ReportController::class, 'costs'])->name('reports.costs');
        Route::get('/reports/staff', [ReportController::class, 'staffPerformance'])->name('reports.staff');
        Route::get('/reports/no-shows', [ReportController::class, 'noShows'])->name('reports.no-shows');
        Route::get('/reports/housekeeping', [ReportController::class, 'housekeeping'])->name('reports.housekeeping');
        Route::get('/reports/maintenance-costs', [ReportController::class, 'maintenanceCosts'])->name('reports.maintenance-costs');
        Route::get('/reports/best-customers', [ReportController::class, 'bestCustomers'])->name('reports.best-customers');
        Route::get('/reports/room-performance', [ReportController::class, 'roomPerformance'])->name('reports.room-performance');
        Route::get('/reports/staff-activity', [ReportController::class, 'staffActivity'])->name('reports.staff-activity');
    });

    // Maintenance (admin/manager only)
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::get('/maintenance/{id}', [MaintenanceController::class, 'show'])->name('maintenance.show');
        Route::put('/maintenance/{id}', [MaintenanceController::class, 'update'])->name('maintenance.update');
        Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    });

    // Infrastructure (admin/manager only)
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/infrastructure', [InfrastructureController::class, 'index'])->name('infrastructure.index');
        Route::get('/infrastructure/create', [InfrastructureController::class, 'create'])->name('infrastructure.create');
        Route::post('/infrastructure', [InfrastructureController::class, 'store'])->name('infrastructure.store');
        Route::get('/infrastructure/{id}', [InfrastructureController::class, 'show'])->name('infrastructure.show');
        Route::get('/infrastructure/{id}/edit', [InfrastructureController::class, 'edit'])->name('infrastructure.edit');
        Route::put('/infrastructure/{id}', [InfrastructureController::class, 'update'])->name('infrastructure.update');
        Route::delete('/infrastructure/{id}', [InfrastructureController::class, 'destroy'])->name('infrastructure.destroy');
        Route::post('/infrastructure/{id}/toggle', [InfrastructureController::class, 'toggleStatus'])->name('infrastructure.toggle');
        Route::post('/infrastructure/sync-iptv', [InfrastructureController::class, 'syncIptv'])->name('infrastructure.sync-iptv');

        // Infrastructure Categories
        Route::get('/infrastructure-categories', [InfrastructureController::class, 'categoriesIndex'])->name('infrastructure.categories.index');
        Route::get('/infrastructure-categories/create', [InfrastructureController::class, 'categoriesCreate'])->name('infrastructure.categories.create');
        Route::post('/infrastructure-categories', [InfrastructureController::class, 'categoriesStore'])->name('infrastructure.categories.store');
        Route::get('/infrastructure-categories/{id}/edit', [InfrastructureController::class, 'categoriesEdit'])->name('infrastructure.categories.edit');
        Route::put('/infrastructure-categories/{id}', [InfrastructureController::class, 'categoriesUpdate'])->name('infrastructure.categories.update');
        Route::delete('/infrastructure-categories/{id}', [InfrastructureController::class, 'categoriesDestroy'])->name('infrastructure.categories.destroy');
    });

    // User Management (admin/manager only)
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Settings (admin/manager only)
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/users/{user}/unlock', [SettingController::class, 'unlockUser'])->name('users.unlock');
    });

    // Roles & Permissions (admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::put('/permissions/{role}', [PermissionController::class, 'update'])->name('permissions.update');
    });
});
