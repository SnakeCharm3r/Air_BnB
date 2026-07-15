<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class AssignRolesToUsersSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roleMap = [
            'admin'        => 'admin',
            'owner'        => 'admin',
            'manager'      => 'manager',
            'receptionist' => 'receptionist',
            'chef'         => 'chef',
        ];

        User::all()->each(function (User $user) use ($roleMap) {
            if ($user->role && isset($roleMap[$user->role])) {
                $user->syncRoles([$roleMap[$user->role]]);
            }
        });

        $this->command->info('Spatie roles assigned to all existing users.');
    }
}
