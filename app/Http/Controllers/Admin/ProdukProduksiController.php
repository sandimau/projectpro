<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProdukStok;
use Illuminate\Http\Request;
use App\Models\ProdukProduksi;
use App\Models\ProduksiProduk;
use App\Models\ProdukProduksiHasil;
use App\Http\Controllers\Controller;

class ProdukProduksiController extends Controller
{
    public function index()
    {
        $produkProduksis = ProdukProduksi::with(['produk.produkModel.kategori', 'user'])
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.produkProduksi.index', compact('produkProduksis'));
    }

    public function create()
    {
        return view('admin.produkProduksi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'satuan' => 'required|in:berat,luas,persen',
            'panjang' => 'nullable|numeric|min:0',
            'lebar' => 'nullable|numeric|min:0',
            'perbandingan' => 'nullable|numeric|min:0',
        ]);

        ProdukProduksi::create([
            'produk_id' => $request->produk_id,
            'satuan' => $request->satuan,
            'panjang' => $request->panjang,
            'lebar' => $request->lebar,
            'perbandingan' => $request->perbandingan ?? $request->panjang * $request->lebar,
            'user_id' => auth()->user()->id ?? null,
        ]);

        return redirect()->route('produkProduksi.index')
            ->with('success', 'Produk Produksi berhasil ditambahkan');
    }

    public function hasilProduksi(ProduksiProduk $produksi)
    {
        return view('admin.produkProduksi.hasilProduksi', compact('produksi'));
    }

    public function hasilProduksiStore(Request $request, ProduksiProduk $produksi)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        ProdukProduksiHasil::create([
            'produk_id' => $request->produk_id,
            'produksi_id' => $produksi->id,
            'jumlah' => $request->jumlah,
            'user_id' => auth()->user()->id ?? null,
        ]);

        return redirect()->route('produksi.show', $produksi->id)
            ->with('success', 'Hasil Produksi berhasil ditambahkan');
    }

    public function destroy(ProdukProduksi $produkProduksi)
    {
        $produkProduksi->delete();

        return redirect()->route('produkProduksi.index')
            ->with('success', 'Produk Produksi berhasil dihapus');
    }

    public function hasilProduksiDestroy(ProduksiProduk $produksi, ProdukProduksiHasil $hasil)
    {
        $hasil->delete();

        return redirect()->route('produksi.show', $produksi->id)
            ->with('success', 'Hasil Produksi berhasil dihapus');
    }

    public function selesaiHasilProduksiSatuan(ProduksiProduk $produksi, ProdukProduksiHasil $hasil)
    {
        if ($hasil->status == 'finish') {
            return redirect()->route('produksi.show', $produksi->id)
                ->with('error', 'Hasil produksi ini sudah diselesaikan');
        }

        $produk = $hasil->produk;
        $produk->updateHpp($hasil->hpp, $hasil->jumlah);

        ProdukStok::create([
            'produk_id' => $produk->id,
            'tambah' => $hasil->jumlah,
            'kurang' => 0,
            'keterangan' => 'hasil produksi',
            'kode' => 'hasilProduksi',
            'detail_id' => $hasil->id
        ]);

        $hasil->update(['status' => 'finish']);

        // Cek apakah semua hasil produksi sudah selesai
        $belumSelesai = $produksi->hasilProduksi()->where('status', '!=', 'finish')->count();
        if ($belumSelesai == 0) {
            $produksi->updated_at = now();
            $produksi->save();
        }

        return redirect()->route('produksi.show', $produksi->id)
            ->with('success', 'Hasil Produksi berhasil diselesaikan');
    }

    public function editHasilProduksi(ProduksiProduk $produksi, ProdukProduksiHasil $hasil)
    {
        $hasil->load('produk.produkModel.kategori');
        return view('admin.produkProduksi.editHasilProduksi', compact('produksi', 'hasil'));
    }

    public function updateHasilProduksi(Request $request, ProduksiProduk $produksi, ProdukProduksiHasil $hasil)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:0',
        ]);

        $hasil->update([
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('produksi.show', $produksi->id)
            ->with('success', 'Hasil Produksi berhasil diupdate');
    }

    public function selesaiProduksi(ProduksiProduk $produksi)
    {
        foreach ($produksi->hasilProduksi as $item) {
            // Skip jika sudah selesai
            if ($item->status == 'finish') {
                continue;
            }

            $produk = $item->produk;
            $produk->updateHpp($item->hpp, $item->jumlah);

            ProdukStok::create([
                'produk_id' => $produk->id,
                'tambah' => $item->jumlah,
                'kurang' => 0,
                'keterangan' => 'hasil produksi',
                'kode' => 'hasilProduksi',
                'detail_id' => $item->id
            ]);

            $item->update(['status' => 'finish']);
        }

        $produksi->updated_at = now();
        $produksi->status = 'finish';
        $produksi->save();

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil diselesaikan');
    }
    public function produksiLagi(ProduksiProduk $produksi)
    {
        $produksiBaru = ProduksiProduk::create([
            'status' => 'proses',
            'cabang_id' => $produksi->cabang_id,
            'ket' => $produksi->ket,
        ]);

        foreach ($produksi->hasilProduksi as $hasil) {
            $produksiBaru->hasilProduksi()->create([
                'produk_id' => $hasil->produk_id,
                'jumlah' => $hasil->jumlah,
            ]);
        }

        foreach ($produksi->bahan as $bahan) {
            $stok = produkStok::create([
                'produk_id' => $bahan->produk_id,
                'kode' => 'bahanProduksi',
                'detail_id' => $produksiBaru->id,
                'kurang' => $bahan->jumlah,
                'keterangan' => $produksiBaru->ket,
            ]);
            $produksiBaru->bahan()->create([
                'produk_id' => $bahan->produk_id,
                'jumlah' => $bahan->jumlah,
                'hpp' => $bahan->hpp,
                'produk_stok_id' => $stok->id,
            ]);
        }

        $produksiBaru->hitungBiaya();

        $produksiBaru->hitungHpp();

        return redirect()->route('produksi.show', $produksiBaru->id)->with('success', 'Produksi berhasil diulang');
    }
}
