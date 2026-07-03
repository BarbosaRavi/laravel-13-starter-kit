<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'permission:sync {--fresh : Delete existing roles and permissions before syncing}';

    protected $description = 'Sync roles and permissions from config';

    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if ($this->option('fresh')) {
            Role::query()->delete();
            Permission::query()->delete();
        }

        $permissions = config('permission_sync.permissions', []);

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $sysAdminRole = Role::findOrCreate('sys_admin');
        $sysAdminRole->syncPermissions(Permission::all());

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Permissions and roles synced.');

        return self::SUCCESS;
    }
}