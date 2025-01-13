<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Member;
use App\Models\Penggajian;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function show()
    {
        return view('auth.profile');
    }

    public function update(ProfileUpdateRequest $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
        ]);

        if ($request->password) {
            auth()->user()->update(['password' => Hash::make($request->password)]);
        }

        auth()->user()->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Profile updated.');
    }

    public function gaji($id)
    {
        $member = Member::where('user_id', $id)->first();
        if ($member) {
            $gajis = Penggajian::where('member_id', $member->id)->paginate(10);
            return view('admin.members.gaji', compact('member', 'gajis'));
        }
        return redirect()->back()->with('error', 'Member not found.');
    }

    public function cuti($id)
    {
        $member = Member::where('user_id', $id)->first();
        if ($member) {
            $cutis = Cuti::where('member_id', $member->id)->paginate(10);
            return view('admin.members.cuti', compact('member', 'cutis'));
        }
        return redirect()->back()->with('error', 'Member not found.');
    }
}
