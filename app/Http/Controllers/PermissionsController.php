<?php

namespace App\Http\Controllers;

use App\Auth\PermissionLevel;
use App\Auth\Permissions;
use App\Auth\RoleDefinitions;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsController extends Controller
{
    public function index()
    {
        $this->ensureCatalogSynced();
        $purged = $this->purgeOrphans();
        $this->grantAllToSuper();

        $menus = Permissions::menus();
        $roles = Role::query()
            ->where('guard_name', Permissions::GUARD)
            ->with('permissions')
            ->orderBy('id')
            ->get();

        $matrix = [];
        $available = [];

        foreach ($menus as $menuKey => $menu) {
            $available[$menuKey] = PermissionLevel::availableForMenu($menu);
            foreach ($roles as $role) {
                $owned = $role->permissions->pluck('name')->all();
                $matrix[$menuKey][$role->id] = PermissionLevel::detect($menu, $owned);
            }
        }

        $levelMeta = PermissionLevel::meta();

        return view('permissions.index', compact(
            'menus',
            'roles',
            'matrix',
            'available',
            'levelMeta',
            'purged'
        ));
    }

    public function saveMatrix(Request $request)
    {
        $menus = Permissions::menus();
        $roles = Role::query()
            ->where('guard_name', Permissions::GUARD)
            ->get()
            ->keyBy('id');

        $payload = $request->input('matrix', []);
        if (! is_array($payload)) {
            $payload = [];
        }

        $validLevels = PermissionLevel::CYCLE;

        $this->ensureCatalogSynced();
        $this->purgeOrphans();

        foreach ($roles as $roleId => $role) {
            $assigned = [];

            foreach ($menus as $menuKey => $menu) {
                $level = $payload[$menuKey][$roleId] ?? PermissionLevel::NONE;
                if ($level === '-' || $level === null) {
                    $level = PermissionLevel::NONE;
                }
                if (! in_array($level, $validLevels, true)) {
                    $level = PermissionLevel::NONE;
                }

                foreach (PermissionLevel::permissionNames($menu, $level) as $name) {
                    $assigned[] = $name;
                }
            }

            // Hanya permission katalog — orphan tidak dipertahankan
            $role->syncPermissions(array_values(array_unique($assigned)));
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Matriks permission berhasil disimpan.');
    }

    public function sync()
    {
        $created = $this->ensureCatalogSynced(true);
        $purged = $this->purgeOrphans();
        $superCount = $this->grantAllToSuper();

        $parts = [];
        if ($created > 0) {
            $parts[] = "{$created} permission baru";
        }
        if ($purged > 0) {
            $parts[] = "{$purged} orphan dihapus";
        }
        $parts[] = "super = ALL ({$superCount})";

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Sinkronisasi selesai: '.implode(', ', $parts).'.');
    }

    private function ensureCatalogSynced(bool $countCreated = false): int
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $created = 0;
        foreach (Permissions::all() as $name) {
            $permission = Permission::findOrCreate($name, Permissions::GUARD);
            if ($countCreated && $permission->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }

    /**
     * Hapus semua permission di DB yang tidak ada di katalog.
     */
    private function purgeOrphans(): int
    {
        $catalog = Permissions::all();

        $orphans = Permission::query()
            ->where('guard_name', Permissions::GUARD)
            ->whereNotIn('name', $catalog)
            ->get();

        $count = $orphans->count();
        foreach ($orphans as $orphan) {
            $orphan->delete();
        }

        if ($count > 0) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }

        return $count;
    }

    /**
     * Role super selalu full akses (ALL permission katalog).
     */
    private function grantAllToSuper(): int
    {
        $role = Role::findOrCreate(RoleDefinitions::SUPER, Permissions::GUARD);
        $all = Permissions::all();
        $role->syncPermissions($all);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return $role->permissions()->count();
    }
}
