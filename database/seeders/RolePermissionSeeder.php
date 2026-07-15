<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'rooms'           => ['view', 'create', 'edit', 'delete'],
            'room-types'      => ['view', 'create', 'edit', 'delete'],
            'bookings'        => ['view', 'create', 'edit', 'delete', 'checkin', 'checkout', 'confirm'],
            'billing'         => ['view', 'create', 'edit'],
            'folios'          => ['view', 'close', 'void'],
            'charges'         => ['view', 'create', 'reverse'],
            'payments'        => ['view', 'create', 'void', 'refund'],
            'invoices'        => ['view', 'create', 'issue', 'cancel', 'void'],
            'receipts'        => ['view', 'create'],
            'inventory'       => ['view', 'create', 'edit', 'delete'],
            'menu'            => ['view', 'create', 'edit', 'delete'],
            'kitchen-orders'  => ['view', 'create', 'edit', 'delete'],
            'staff'           => ['view', 'create', 'edit', 'delete'],
            'costs'           => ['view', 'create', 'edit', 'delete'],
            'reports'         => ['view'],
            'maintenance'     => ['view', 'create', 'edit', 'delete'],
            'infrastructure'  => ['view', 'create', 'edit', 'delete'],
            'users'           => ['view', 'create', 'edit', 'delete'],
            'settings'        => ['view', 'edit'],
            'tasks'           => ['view', 'create', 'edit', 'delete'],
            'finance'         => ['view'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }

        // ----- ADMIN: everything -----
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::where('guard_name', 'web')->get());

        // ----- MANAGER: everything except users.delete and settings -----
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions(
            Permission::where('guard_name', 'web')
                ->whereNotIn('name', ['users.delete', 'settings.edit'])
                ->get()
        );

        // ----- RECEPTIONIST: front-desk operations -----
        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $receptionist->syncPermissions([
            'rooms.view', 'rooms.create', 'rooms.edit',
            'room-types.view',
            'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.checkin', 'bookings.checkout', 'bookings.confirm',
            'billing.view', 'billing.create',
            'folios.view', 'folios.close',
            'charges.view', 'charges.create',
            'payments.view', 'payments.create',
            'invoices.view', 'invoices.create', 'invoices.issue',
            'receipts.view', 'receipts.create',
            'inventory.view', 'inventory.create', 'inventory.edit',
            'menu.view', 'menu.create',
            'kitchen-orders.view', 'kitchen-orders.create', 'kitchen-orders.edit',
            'tasks.view', 'tasks.create', 'tasks.edit',
        ]);

        // ----- CHEF: kitchen-focused -----
        $chef = Role::firstOrCreate(['name' => 'chef', 'guard_name' => 'web']);
        $chef->syncPermissions([
            'inventory.view', 'inventory.create', 'inventory.edit',
            'menu.view', 'menu.create', 'menu.edit',
            'kitchen-orders.view', 'kitchen-orders.create', 'kitchen-orders.edit', 'kitchen-orders.delete',
            'tasks.view', 'tasks.create', 'tasks.edit',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
