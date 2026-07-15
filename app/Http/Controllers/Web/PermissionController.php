<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    private array $modules = [
        'rooms'          => 'Rooms',
        'room-types'     => 'Room Types',
        'bookings'       => 'Bookings',
        'billing'        => 'Billing',
        'folios'         => 'Folios',
        'charges'        => 'Charges',
        'payments'       => 'Payments',
        'invoices'       => 'Invoices',
        'receipts'       => 'Receipts',
        'inventory'      => 'Inventory',
        'menu'           => 'Menu',
        'kitchen-orders' => 'Kitchen Orders',
        'staff'          => 'Staff',
        'costs'          => 'Costs',
        'reports'        => 'Reports',
        'maintenance'    => 'Maintenance',
        'infrastructure' => 'Infrastructure',
        'users'          => 'User Management',
        'settings'       => 'Settings',
        'tasks'          => 'Tasks',
        'finance'        => 'Finance',
    ];

    public function index()
    {
        $roles = Role::with('permissions')->where('guard_name', 'web')->get();
        $permissions = Permission::where('guard_name', 'web')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return view('permissions.index', [
            'roles'   => $roles,
            'permissions' => $permissions,
            'modules' => $this->modules,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('permissions.index')
            ->with('success', "Permissions for role \"{$role->name}\" updated successfully.");
    }
}
