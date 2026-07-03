<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionModel = config('permission.models.permission');
        $roleModel = config('permission.models.role');

        $permissions = config('permission_sync.permissions', []);
        $roles = config('permission_sync.roles', []);

        foreach ($permissions as $permission) {
            $permissionModel::findOrCreate($permission);
        }

        $allPermissions = $permissionModel::query()
            ->pluck('name')
            ->all();

        foreach ($roles as $roleName => $rolePermissions) {
            $role = $roleModel::findOrCreate($roleName);

            if ($roleName === 'sys_admin' || $rolePermissions === ['*']) {
                $role->syncPermissions($allPermissions);

                continue;
            }

            $role->syncPermissions($rolePermissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}