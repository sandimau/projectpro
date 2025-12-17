<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProdukPakai;
use App\Models\ProdukStok;
use App\Models\Produk;
use Illuminate\Http\Request;

class PemakaianController extends Controller
{
    public function index(Request $request)
    {
        $pemakaians = ProdukPakai::query()
            ->with(['produk.produkModel.kategori', 'user'])
            ->when($request->dari && $request->sampai, function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->dari, $request->sampai]);
            })
            ->when($request->produk_id, function ($query) use ($request) {
                $query->where('produk_id', $request->produk_id);
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->appends($request->only(['dari', 'sampai', 'produk_id']));

        return view('admin.pemakaian.index', compact('pemakaians'));
    }

    public function create()
    {
        return view('admin.pemakaian.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        $hpp = $produk->hpp ?? 0;

        // Buat ProdukStok untuk mengurangi stok
        $produkStok = ProdukStok::create([
            'produk_id' => $request->produk_id,
            'tambah' => 0,
            'kurang' => $request->jumlah,
            'keterangan' => $request->keterangan ?? 'Pemakaian produk',
            'kode' => 'pakai',
            'user_id' => auth()->user()->id,
        ]);

        // Buat ProdukPakai
        $pemakaian = ProdukPakai::create([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'hpp' => $hpp,
            'produk_stok_id' => $produkStok->id,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->route('pemakaian.index')->withSuccess(__('Pemakaian berhasil ditambahkan'));
    }

    public function edit(ProdukPakai $pemakaian)
    {
        return view('admin.pemakaian.edit', compact('pemakaian'));
    }

    public function update(Request $request, ProdukPakai $pemakaian)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        $hpp = $produk->hpp ?? 0;

        // Kembalikan stok produk lama jika ada ProdukStok yang terkait
        if ($pemakaian->produk_stok_id) {
            $oldProdukStok = ProdukStok::find($pemakaian->produk_stok_id);
            if ($oldProdukStok) {
                // Kembalikan stok yang sudah dikurangi
                $produkStok = ProdukStok::create([
                    'produk_id' => $pemakaian->produk_id,
                    'tambah' => $pemakaian->jumlah,
                    'kurang' => 0,
                    'keterangan' => 'Balikin pemakaian - ' . $request->keterangan,
                    'kode' => 'pakai',
                    'user_id' => auth()->user()->id,
                    'detail_id' => $pemakaian->id,
                    'status' => 'manual',
                ]);

                $oldProdukStok->update([
                    'detail_id' => $pemakaian->id,
                    'status' => 'manual',
                ]);
            }
        }

        // Update ProdukPakai dengan ProdukStok yang baru
        $pemakaian->update([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah - $pemakaian->jumlah,
            'keterangan' => $request->keterangan,
            'hpp' => $hpp,
            'produk_stok_id' => $produkStok->id,
        ]);

        return redirect()->route('pemakaian.index')->withSuccess(__('Pemakaian berhasil diupdate'));
    }
}
