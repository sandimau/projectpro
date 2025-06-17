<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ProdukKategoriUtama;
use App\Models\ProdukKategori;
use App\Models\ProdukModel;
use App\Http\Controllers\Controller;

class ProdukKategoriUtamaController extends Controller
{
    public function index()
    {
        $kategoriUtamas = ProdukKategoriUtama::latest()->paginate(10);
        return view('produk-kategori-utama.index', compact('kategoriUtamas'));
    }

    public function create()
    {
        return view('produk-kategori-utama.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jual' => 'nullable|boolean',
            'beli' => 'nullable|boolean',
            'stok' => 'nullable|boolean',
            'produksi' => 'nullable|boolean',
        ]);

        ProdukKategoriUtama::create($request->all());

        return redirect()->route('produk-kategori-utama.index')
            ->with('success', 'Kategori Utama berhasil ditambahkan');
    }

    public function edit(ProdukKategoriUtama $produkKategoriUtama)
    {
        return view('produk-kategori-utama.edit', compact('produkKategoriUtama'));
    }

    public function update(Request $request, ProdukKategoriUtama $produkKategoriUtama)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jual' => 'nullable|boolean',
            'beli' => 'nullable|boolean',
            'stok' => 'nullable|boolean',
            'produksi' => 'nullable|boolean',
        ]);

        $produkKategoriUtama->update($request->all());

        // Update semua produk model yang berelasi dengan kategori utama ini
        $kategoriIds = ProdukKategori::where('kategori_utama_id', $produkKategoriUtama->id)->pluck('id');
        ProdukModel::whereIn('kategori_id', $kategoriIds)->update([
            'jual' => $request->jual,
            'beli' => $request->beli,
            'stok' => $request->stok,
            'produksi' => $request->produksi
        ]);

        return redirect()->route('produk-kategori-utama.index')
            ->with('success', 'Kategori Utama berhasil diperbarui');
    }

    public function destroy(ProdukKategoriUtama $produkKategoriUtama)
    {
        $produkKategoriUtama->delete();

        return redirect()->route('produk-kategori-utama.index')
            ->with('success', 'Kategori Utama berhasil dihapus');
    }
}
