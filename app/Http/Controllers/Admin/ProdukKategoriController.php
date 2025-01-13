<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProdukKategori;
use App\Models\ProdukKategoriUtama;
use Illuminate\Http\Request;

class ProdukKategoriController extends Controller
{
    public function index()
    {

        $currentUrl = request()->fullUrl();
        $lastNumber = preg_replace('/[^0-9]/', '', substr($currentUrl, strrpos($currentUrl, '?') + 1));
        $kategoriUtama = ProdukKategoriUtama::find($lastNumber);
        $kategoris = ProdukKategori::with('kategoriUtama')->where('kategori_utama_id', $kategoriUtama->id)->get();
        return view('produk-kategori.index', compact('kategoris', 'kategoriUtama'));
    }

    public function create()
    {
        $utama = ProdukKategoriUtama::find(request()->input('kategori_utama_id'));
        $kategoriUtamas = ProdukKategoriUtama::all();
        return view('produk-kategori.create', compact('kategoriUtamas', 'utama'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_utama_id' => 'nullable|exists:produk_kategori_utamas,id'
        ]);

        ProdukKategori::create($request->all());
        $kategoriUtama = ProdukKategoriUtama::find($request->kategori_utama_id);
        return redirect()->route('produk-kategori.index', ['kategori_utama_id' => $kategoriUtama->id])->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit(ProdukKategori $produkKategori)
    {
        $kategoriUtamas = ProdukKategoriUtama::all();
        return view('produk-kategori.edit', compact('produkKategori', 'kategoriUtamas'));
    }

    public function update(Request $request, ProdukKategori $produkKategori)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_utama_id' => 'nullable|exists:produk_kategori_utamas,id'
        ]);

        $produkKategori->update($request->all());
        $kategoriUtama = ProdukKategoriUtama::find($request->kategori_utama_id);
        return redirect()->route('produk-kategori.index', ['kategori_utama_id' => $kategoriUtama->id])->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(ProdukKategori $produkKategori)
    {
        $produkKategori->delete();
        return redirect()->route('produk-kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}
