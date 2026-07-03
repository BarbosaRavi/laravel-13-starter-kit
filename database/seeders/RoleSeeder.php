<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
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

        foreach ($permissions as $permission) {
            $permissionModel::findOrCreate($permission);
        }

        $allPermissions = $permissionModel::query()
            ->pluck('name')
            ->all();

        $rolePermissions = [
            UserTypeEnum::SYS_ADMIN->value => ['*'],

            UserTypeEnum::USER->value => [
                'users.view',
            ],
        ];

        foreach (UserTypeEnum::cases() as $userType) {
            $role = $roleModel::findOrCreate($userType->value);

            $permissions = $rolePermissions[$userType->value] ?? [];

            if ($permissions === ['*']) {
                $role->syncPermissions($allPermissions);

                continue;
            }

            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}