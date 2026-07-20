<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        $user->syncRoles([(int) $request->validated('role')]);

        return redirect()
            ->route('users.index')
            ->withSuccess(__('User created successfully.'));
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'userRole' => $user->roles->pluck('name')->toArray(),
            'roles' => Role::orderBy('name')->get(),
            'members' => Member::query()->orderBy('nama_lengkap')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->only('name', 'email'));
        $user->syncRoles([(int) $request->validated('role')]);

        if ($request->filled('member_id')) {
            Member::where('user_id', $user->id)->update(['user_id' => null]);

            Member::whereKey($request->validated('member_id'))->update([
                'user_id' => $user->id,
            ]);
        }

        return redirect()
            ->route('users.index')
            ->withSuccess(__('User updated successfully.'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
    }
}
