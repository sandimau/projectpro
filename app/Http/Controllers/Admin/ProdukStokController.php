<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\ProdukStok;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProdukStokController extends Controller
{
    public function index(Produk $produk)
    {
        abort_if(Gate::denies('produk_stok_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $produkStoks = ProdukStok::where('produk_id', $produk->id)->orderBy('id', 'desc')->get();

        return view('admin.produkStoks.index', compact('produkStoks', 'produk'));
    }

    public function create(Produk $produk)
    {
        abort_if(Gate::denies('produk_stok_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.produkStoks.create', compact('produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tambah' => 'required',
            'kurang' => 'required',
            'keterangan' => 'required',
        ]);

        ProdukStok::create([
            'created_at' => $request->tanggal,
            'tambah' => $request->tambah,
            'kurang' => $request->kurang,
            'keterangan' => $request->keterangan,
            'kode' => 'opn',
            'produk_id' => $request->produk_id,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->route('produkStok.index', $request->produk_id)->withSuccess(__('Produk Stok berhasil diupdate'));
    }
}
