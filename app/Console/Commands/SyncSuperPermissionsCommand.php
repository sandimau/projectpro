<?php

namespace App\Console\Commands;

use App\Auth\Permissions;
use App\Auth\RoleDefinitions;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncSuperPermissionsCommand extends Command
{
    protected $signature = 'rbac:sync-super
                            {--user= : Email user yang di-assign role super (opsional)}';

    protected $description = 'Sync semua permission katalog ke role super di database';

    public function handle(): int
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $created = 0;
        foreach (Permissions::all() as $name) {
            $permission = Permission::findOrCreate($name, Permissions::GUARD);
            if ($permission->wasRecentlyCreated) {
                $created++;
            }
        }

        $role = Role::findOrCreate(RoleDefinitions::SUPER, Permissions::GUARD);
        $all = Permissions::all();
        $role->syncPermissions($all);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $email = $this->option('user');
        if ($email) {
            $user = User::where('email', $email)->first();
            if (! $user) {
                $this->error("User {$email} tidak ditemukan.");

                return self::FAILURE;
            }
            $user->syncRoles([RoleDefinitions::SUPER]);
            $this->info("User {$email} di-assign role super.");
        } else {
            // Pastikan semua user yang sudah punya role super tetap sinkron
            User::role(RoleDefinitions::SUPER)->each(function (User $user) {
                $user->assignRole(RoleDefinitions::SUPER);
            });
        }

        $this->info("Permission katalog: ".count($all)." (baru: {$created}).");
        $this->info("Role 'super' sekarang punya {$role->permissions()->count()} permission (ALL).");

        return self::SUCCESS;
    }
}
