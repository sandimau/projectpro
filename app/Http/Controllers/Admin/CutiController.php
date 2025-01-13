<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCutiRequest;
use App\Http\Requests\StoreCutiRequest;
use App\Http\Requests\UpdateCutiRequest;
use App\Models\Cuti;
use App\Models\Member;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CutiController extends Controller
{
    public function create(Member $member)
    {
        return view('admin.cutis.create', compact('member'));
    }

    public function createIjin(Member $member)
    {
        return view('admin.cutis.createIjin', compact('member'));
    }

    function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'keterangan' => 'required',
        ]);

        Cuti::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'cuti' => 1,
            'member_id' => $request->member_id,
        ]);

        return redirect()->route('members.cuti', $request->member_id)->withSuccess(__('Cuti created successfully.'));
    }

    function storeIjin(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'keterangan' => 'required',
        ]);

        Cuti::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'cuti' => 0,
            'member_id' => $request->member_id,
        ]);

        return redirect()->route('members.ijin', $request->member_id)->withSuccess(__('Ijin created successfully.'));
    }

    public function edit(Cuti $cuti)
    {
        $cuti = $cuti;
        return view('admin.cutis.edit', compact('cuti'));
    }

    public function update(Request $request, Cuti $cuti)
    {
        $cuti->update($request->all());

        return redirect()->route('members.show', $request->member_id)->withSuccess(__('Cuti updated successfully.'));
    }
}
