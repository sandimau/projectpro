<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kasbon;
use App\Models\Member;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KasbonController extends Controller
{
    public function create(Member $member)
    {
        $kas = AkunDetail::pluck('nama', 'id')->toArray();
        return view('admin.kasbons.create', compact('member', 'kas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'jumlah' => 'required',
            'keterangan' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            //get kasbon sebelumnya
            $kasbon = Kasbon::where('member_id', $request->member_id)->orderBy('id', 'DESC')->first();
            if (!empty($kasbon)) {
                $saldoAwal = $kasbon->saldo;
            } else {
                $saldoAwal = 0;
            }

            $data['pemasukan'] = $request->jumlah;
            $data['keterangan'] = $request->keterangan;
            $data['member_id'] = $request->member_id;
            $data['created_at'] = $request->tanggal;
            $data['saldo'] = $saldoAwal + $request->jumlah;

            //insert kasbon
            Kasbon::create($data);

            //update saldo akun detail
            $akunDetail = AkunDetail::where('id', $request->akun_detail_id)->first();
            $saldo = $akunDetail->saldo;
            $update = $saldo - $request->jumlah;
            $akunDetail->update([
                'saldo' => $update,
            ]);

            //get nama member untuk ket kasbon
            $member = Member::where('id', $request->member_id)->first();

            //insert into buku besar table
            BukuBesar::insert([
                'akun_detail_id' => $request->akun_detail_id,
                'ket' => 'kasbon ke ' . $member->nama_lengkap,
                'kredit' => $request->jumlah,
                'debet' => 0,
                'saldo' => $update,
                'created_at' => Carbon::now(),
            ]);
        });

        return redirect()->route('members.kasbon', $request->member_id)->withSuccess(__('Kasbon created successfully.'));
    }

    public function bayar(Member $member)
    {
        $members = $member;
        $kas = AkunDetail::pluck('nama', 'id')->toArray();
        $kasbon = Kasbon::where('member_id',$members->id)->first();
        return view('admin.kasbons.bayar', compact('members', 'kas','kasbon'));
    }

    public function bayarStore(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'jumlah' => 'required',
            'keterangan' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            //get kasbon sebelumnya
            $kasbon = Kasbon::where('member_id', $request->member_id)->orderBy('id', 'DESC')->first();
            if (!empty($kasbon)) {
                $saldoAwal = $kasbon->saldo;
            } else {
                $saldoAwal = 0;
            }

            $data['pengeluaran'] = $request->jumlah;
            $data['keterangan'] = $request->keterangan;
            $data['member_id'] = $request->member_id;
            $data['created_at'] = $request->tanggal;
            $data['saldo'] = $saldoAwal - $request->jumlah;

            //insert kasbon
            Kasbon::create($data);

            //update saldo akun detail
            $akunDetail = AkunDetail::where('id', $request->akun_detail_id)->first();
            $saldo = $akunDetail->saldo;
            $update = $saldo + $request->jumlah;
            $akunDetail->update([
                'saldo' => $update,
            ]);

            //get nama member untuk ket kasbon
            $member = Member::where('id', $request->member_id)->first();

            //insert into buku besar table
            BukuBesar::insert([
                'akun_detail_id' => $request->akun_detail_id,
                'ket' => 'bayar kasbon dari ' . $member->nama_lengkap,
                'kredit' => 0,
                'debet' => $request->jumlah,
                'saldo' => $update,
                'created_at' => Carbon::now(),
            ]);
        });

        return redirect()->route('members.show', $request->member_id)->withSuccess(__('Kasbon created successfully.'));
    }
}
