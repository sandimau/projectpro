<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsToMemberModal;
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
    use RespondsToMemberModal;
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

        return $this->memberModalResponse(
            $request,
            __('Cuti created successfully.'),
            route('members.cuti', $request->member_id)
        );
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

        return $this->memberModalResponse(
            $request,
            __('Ijin created successfully.'),
            route('members.ijin', $request->member_id)
        );
    }

    public function edit(Cuti $cuti)
    {
        $cuti = $cuti;
        return view('admin.cutis.edit', compact('cuti'));
    }

    public function update(Request $request, Cuti $cuti)
    {
        $cuti->update($request->all());

        return $this->memberModalResponse(
            $request,
            __('Cuti updated successfully.'),
            route('members.show', $request->member_id)
        );
    }
}
