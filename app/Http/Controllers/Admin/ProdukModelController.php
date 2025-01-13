<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProdukModel;
use App\Models\ProdukKategori;
use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukModelController extends Controller
{
    public function index()
    {
        $produks = ProdukModel::with(['kategori', 'kontak'])->get();
        return view('produkModel.index', compact('produks'));
    }

    public function create()
    {
        $kategoris = ProdukKategori::all();
        $kontaks = Kontak::all();
        return view('produkModel.create', compact('kategoris', 'kontaks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric',
            'satuan' => 'required',
            'deskripsi' => 'nullable',
            'jual' => 'nullable',
            'beli' => 'nullable',
            'stok' => 'nullable|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategori_id' => 'required',
            'kontak_id' => 'required'
        ]);

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $path = $gambar->store('public/produk');
            $validated['gambar'] = str_replace('public/', '', $path);
        }

        ProdukModel::create($validated);
        return redirect()->route('produkModel.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(ProdukModel $produk)
    {
        $kategoris = ProdukKategori::all();
        $kontaks = Kontak::all();
        return view('produkModel.edit', compact('produk', 'kategoris', 'kontaks'));
    }

    public function update(Request $request, ProdukModel $produk)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric',
            'satuan' => 'required',
            'deskripsi' => 'nullable',
            'jual' => 'nullable',
            'beli' => 'nullable',
            'stok' => 'nullable|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategori_id' => 'required',
            'kontak_id' => 'required'
        ]);

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) {
                Storage::delete('public/' . $produk->gambar);
            }
            $gambar = $request->file('gambar');
            $path = $gambar->store('public/produk');
            $validated['gambar'] = str_replace('public/', '', $path);
        }

        $produk->update($validated);
        return redirect()->route('produkModel.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(ProdukModel $produk)
    {
        if ($produk->gambar) {
            Storage::delete('public/' . $produk->gambar);
        }
        $produk->delete();
        return redirect()->route('produkModel.index')->with('success', 'Produk berhasil dihapus');
    }
}
