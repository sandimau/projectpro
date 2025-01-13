<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use App\Models\ProdukModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProdukController extends Controller
{
    public function create()
    {
        $produkModels = ProdukModel::all();
        return view('produk.create', compact('produkModels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'hpp' => 'required|numeric',
            'status' => 'required|in:0,1',
            'produk_model_id' => 'required|exists:produk_models,id'
        ]);

        Produk::create($request->all());
        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Produk $produk)
    {
        $produkModels = ProdukModel::all();
        return view('produk.edit', compact('produk', 'produkModels'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama' => 'required',
            'hpp' => 'required|numeric',
            'status' => 'required|in:0,1',
            'produk_model_id' => 'required|exists:produk_models,id'
        ]);

        $produk->update($request->all());
        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }
}
