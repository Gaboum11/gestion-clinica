<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage-users',
            'delete-appointments',
            'update-medical-records',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $adminRole = Role::findOrCreate('Admin', 'web');
        $doctorRole = Role::findOrCreate('Medico', 'web');
        $assistantRole = Role::findOrCreate('Asistente', 'web');

        $adminRole->syncPermissions($permissions);
        $doctorRole->syncPermissions(['update-medical-records']);
        $assistantRole->syncPermissions([]);

        User::query()->where('role', 'admin')->get()->each(function (User $user) {
            $user->syncRoles(['Admin']);
        });

        User::query()->where('role', 'doctor')->get()->each(function (User $user) {
            $user->syncRoles(['Medico']);
        });

        User::query()->where('role', 'assistant')->get()->each(function (User $user) {
            $user->syncRoles(['Asistente']);
        });
    }
}
