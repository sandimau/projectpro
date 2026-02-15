<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lembur;
use App\Models\Member;
use Illuminate\Http\Request;

class LemburController extends Controller
{
    // Helper untuk nama bulan
    protected function getBulans()
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    public function create(Member $member)
    {
        $bulans = $this->getBulans();
        return view('admin.lemburs.create', compact('member', 'bulans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'keterangan' => 'required|string',
            'jam' => 'required|numeric|min:0',
        ]);

        Lembur::create([
            'tahun' => date("Y"),
            'bulan' => now()->month,
            'keterangan' => $request->keterangan,
            'jam' => $request->jam,
            'member_id' => $request->member_id,
            'dibayar' => 'belum',
            'status' => 'waiting',
        ]);

        return redirect()->route('members.lembur', $request->member_id)->with('success', __('Lembur created successfully.'));
    }

    public function edit(Lembur $lembur)
    {
        $bulans = $this->getBulans();
        return view('admin.lemburs.edit', compact('lembur', 'bulans'));
    }

    public function update(Request $request, Lembur $lembur)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'keterangan' => 'required|string',
            'jam' => 'required|numeric|min:0',
            'member_id' => 'required|exists:members,id',
        ]);

        $lembur->update([
            'bulan' => $request->bulan,
            'keterangan' => $request->keterangan,
            'jam' => $request->jam,
            'member_id' => $request->member_id,
            // Tidak memperbarui tahun dan dibayar pada update
        ]);

        return redirect()->route('members.show', $request->member_id)->with('success', __('Lembur updated successfully.'));
    }

    public function approve(Lembur $lembur)
    {
        $lembur->update([
            'status' => 'approved',
        ]);

        return redirect()->route('members.lembur', $lembur->member_id)->with('success', __('Lembur approved successfully.'));
    }

    public function reject(Lembur $lembur)
    {
        $lembur->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('members.lembur', $lembur->member_id)->with('success', __('Lembur approved successfully.'));
    }
}
