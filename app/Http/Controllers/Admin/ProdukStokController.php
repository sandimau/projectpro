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

    public function opname(Request $request)
    {
        if ($request->dari == null && $request->sampai == null  && $request->produk_id == null) {
            $produkStoks = ProdukStok::where('kode', 'opn')->orderBy('id', 'desc')->paginate(10);
        } else {
            $produkStoks = ProdukStok::query()
                ->when($request->dari && $request->sampai, function ($query) use ($request) {
                    $query->whereBetween('created_at', [$request->dari, $request->sampai]);
                })
                ->when($request->produk_id, function ($query) use ($request) {
                    $query->where('produk_id', $request->produk_id);
                })
                ->where('kode', 'opn')
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->appends(['dari' => $request->dari, 'sampai' => $request->sampai, 'produk_id' => $request->produk_id]);
        }

        $dari = null;
        $sampai = null;

        if ($request->bulan) {
            $dari = $request->bulan . '-01';
            $sampai = date('Y-m-t', strtotime($request->bulan));
            $produkStoks = ProdukStok::query()
                ->when($dari && $sampai, function ($query) use ($dari, $sampai) {
                    $query->whereBetween('created_at', [$dari, $sampai]);
                })
                ->where('kode', 'opn')
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->appends(['dari' => $request->dari, 'sampai' => $request->sampai]);
        }

        return view('admin.produkStoks.opname', compact('produkStoks','dari','sampai'));
    }
}
