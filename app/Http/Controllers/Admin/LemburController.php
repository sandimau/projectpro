<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lembur;
use App\Models\Member;
use Illuminate\Http\Request;

class LemburController extends Controller
{
    public function create(Member $member)
    {
        $bulans = [
            1 => 'januari',
            2 => 'februari',
            3 => 'maret', 'april',
            4 => 'mei',
            5 => 'juni',
            6 => 'juli',
            7 => 'agustus',
            8 => 'september',
            9 => 'oktober',
            10 => 'november',
            11 => 'desember',
        ];
        return view('admin.lemburs.create', compact('member', 'bulans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'keterangan' => 'required',
            'jam' => 'required',
        ]);

        Lembur::create([
            'tahun' => date("Y"),
            'bulan' => $request->bulan,
            'keterangan' => $request->keterangan,
            'jam' => $request->jam,
            'member_id' => $request->member_id,
            'dibayar' => 'belum',
        ]);

        return redirect()->route('members.lembur', $request->member_id)->withSuccess(__('Lembur created successfully.'));
    }

    public function edit(Lembur $lembur)
    {
        $bulans = [
            1 => 'januari',
            2 => 'februari',
            3 => 'maret', 'april',
            4 => 'mei',
            5 => 'juni',
            6 => 'juli',
            7 => 'agustus',
            8 => 'september',
            9 => 'oktober',
            10 => 'november',
            11 => 'desember',
        ];
        $lembur = $lembur;
        return view('admin.lemburs.edit', compact('lembur', 'bulans'));
    }

    public function update(Request $request, Lembur $lembur)
    {
        $lembur->update($request->all());

        return redirect()->route('members.show', $request->member_id)->withSuccess(__('Lembur updated successfully.'));
    }
}
