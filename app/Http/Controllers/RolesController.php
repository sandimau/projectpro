<?php

namespace App\Http\Controllers;

use App\Auth\Permissions;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesController extends Controller
{
    public function index()
    {
        return redirect()->route('permissions.index');
    }

    public function create()
    {
        return redirect()->route('permissions.index');
    }

    public function store(StoreRoleRequest $request)
    {
        Role::create([
            'name' => $request->validated('name'),
            'guard_name' => Permissions::GUARD,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Role berhasil dibuat. Atur akses di matriks di bawah.');
    }

    public function show(Role $role)
    {
        return redirect()->route('permissions.index');
    }

    public function edit(Role $role)
    {
        return redirect()->route('permissions.index');
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        if ($role->name === 'super' && $request->validated('name') !== 'super') {
            return redirect()
                ->route('permissions.index')
                ->with('error', 'Nama role super tidak boleh diubah.');
        }

        $role->update(['name' => $request->validated('name')]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super') {
            return redirect()
                ->route('permissions.index')
                ->with('error', 'Role super tidak boleh dihapus.');
        }

        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}
