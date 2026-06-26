<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsToMemberModal;
use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Member;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    use RespondsToMemberModal;

    private function cutiValidationRules(): array
    {
        return [
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'member_id' => 'required|exists:members,id',
        ];
    }

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
        $this->validateMemberModal($request, $this->cutiValidationRules());

        Cuti::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'cuti' => 1,
            'member_id' => $request->member_id,
        ]);

        return $this->memberModalResponse(
            $request,
            __('Cuti berhasil ditambahkan.'),
            route('members.cuti', $request->member_id)
        );
    }

    function storeIjin(Request $request)
    {
        $this->validateMemberModal($request, $this->cutiValidationRules());

        Cuti::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'cuti' => 0,
            'member_id' => $request->member_id,
        ]);

        return $this->memberModalResponse(
            $request,
            __('Ijin berhasil ditambahkan.'),
            route('members.ijin', $request->member_id)
        );
    }

    public function edit(Cuti $cuti)
    {
        return view('admin.cutis.edit', compact('cuti'));
    }

    public function update(Request $request, Cuti $cuti)
    {
        $this->validateMemberModal($request, array_merge($this->cutiValidationRules(), [
            'cuti' => 'required|in:0,1',
        ]));

        $cuti->update([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'cuti' => $request->cuti,
        ]);

        $redirectRoute = $request->cuti == '1'
            ? route('members.cuti', $request->member_id)
            : route('members.ijin', $request->member_id);

        $message = $request->cuti == '1'
            ? __('Cuti berhasil diperbarui.')
            : __('Ijin berhasil diperbarui.');

        return $this->memberModalResponse($request, $message, $redirectRoute);
    }
}
