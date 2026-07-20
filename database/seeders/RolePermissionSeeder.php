<?php

namespace Database\Seeders;

use App\Auth\Permissions;
use App\Auth\RoleDefinitions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (RoleDefinitions::definitions() as $roleName => $_) {
            $role = Role::findOrCreate($roleName, Permissions::GUARD);
            $permissions = RoleDefinitions::permissionsFor($roleName);

            // super selalu diselaraskan ke seluruh katalog
            if ($roleName === RoleDefinitions::SUPER) {
                $role->syncPermissions($permissions);
                continue;
            }

            // role lain hanya diisi jika masih kosong (aman untuk DB existing)
            if ($role->permissions()->count() === 0) {
                $role->syncPermissions($permissions);
            }
        }
    }
}
