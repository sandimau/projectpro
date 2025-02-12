<?php

namespace App\Http\Controllers\Admin;

use App\Models\Hutang;
use App\Models\Kontak;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use App\Models\HutangDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HutangController extends Controller
{
    public function index()
    {
        $hutangs = Hutang::with('kontak')->latest()->paginate(10);
        return view('admin.hutang.index', compact('hutangs'));
    }

    public function create()
    {
        $kontaks = Kontak::all();
        $jenis = request()->jenis;
        $kas = AkunDetail::kas()->get();

        return view('admin.hutang.create', compact('kontaks', 'jenis', 'kas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kontak_id' => 'required',
            'akun_detail_id' => 'required',
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'jenis' => 'required|in:hutang,piutang',
        ]);

        $hutang = Hutang::create($validated);

        $debet = $validated['jenis'] == 'hutang' ? $validated['jumlah'] : 0;
        $kredit = $validated['jenis'] == 'hutang' ? 0 : $validated['jumlah'];
        $keterangan = $validated['jenis'] == 'hutang' ? 'Hutang dari ' . $hutang->kontak->nama : 'Piutang ke ' . $hutang->kontak->nama;

        BukuBesar::create([
            'akun_detail_id' => $validated['akun_detail_id'],
            'kode' => $validated['jenis'] == 'hutang' ? 'htg' : 'ptg',
            'debet' => $debet,
            'kredit' => $kredit,
            'ket' => $keterangan,
            'detail_id' => $hutang->id,
        ]);

        return redirect()->route('hutang.index')
            ->with('success', request()->jenis == 'hutang' ? 'Hutang berhasil ditambahkan' : 'Piutang berhasil ditambahkan');
    }

    public function bayar(Hutang $hutang)
    {
        $kontaks = Kontak::all();
        $kas = AkunDetail::kas()->get();
        return view('admin.hutang.bayar', compact('hutang', 'kontaks', 'kas'));
    }

    public function bayarStore(Request $request)
    {
        $validated = $request->validate([
            'hutang_id' => 'required',
            'akun_detail_id' => 'required',
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $hutang = Hutang::find($validated['hutang_id']);

        HutangDetail::create($validated);

        $kredit = request()->jenis == 'hutang' ? $validated['jumlah'] : 0;
        $debet = request()->jenis == 'hutang' ? 0 : $validated['jumlah'];
        $keterangan = request()->jenis == 'hutang' ? 'Bayar Hutang ke ' . $hutang->kontak->nama : 'Bayar Piutang dari ' . $hutang->kontak->nama;

        BukuBesar::create([
            'akun_detail_id' => $validated['akun_detail_id'],
            'kode' => request()->jenis == 'hutang' ? 'htg' : 'ptg',
            'debet' => $debet,
            'kredit' => $kredit,
            'ket' => $keterangan,
            'detail_id' => $hutang->id,
        ]);

        return redirect()->route('hutang.index')
            ->with('success', request()->jenis == 'hutang' ? 'Hutang berhasil dibayar' : 'Piutang berhasil dibayar');
    }
    public function detail(Hutang $hutang)
    {
        return view('admin.hutang.detail', compact('hutang'));
    }
}
