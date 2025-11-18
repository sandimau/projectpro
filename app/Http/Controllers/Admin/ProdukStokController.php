<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Order;
use App\Models\Produk;
use App\Models\ProdukStok;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            'tanggal' => 'required',
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

        return view('admin.produkStoks.opname', compact('produkStoks', 'dari', 'sampai'));
    }
    public function editStore(ProdukStok $produkStok)
    {
        abort_if(Gate::denies('opname_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($produkStok->detail_id) {
            $order = Order::find($produkStok->detail_id);
            $ket = 'barang dikembalikan dari ' .$order->kontak->nama.' '.$order->konsumen_detail .' ('.$order->nota.')';
            $detail_id = $produkStok->detail_id;
        } else {
            // Ambil keterangan dan ekstrak kata setelah "oleh" jika ada
            $ket = $produkStok->keterangan;
            if (strpos($ket, 'oleh') !== false) {
                $parts = explode('oleh', $ket, 2);
                // ambil bagian setelah "oleh", lalu ambil kata pertama
                $afterOleh = trim($parts[1]);
                $firstWord = strtok($afterOleh, " ");
                $ket = $firstWord;
                $order = Order::where('konsumen_detail', $ket)->first();
                $ket = 'barang dikembalikan dari ' .$order->kontak->nama.' '.$order->konsumen_detail .' ('.$order->nota.')';
                $detail_id = $order->id;
            }
        }

        ProdukStok::create([
            'tambah' => $produkStok->kurang,
            'kurang' => 0,
            'keterangan' => $ket,
            'kode' => 'btl',
            'produk_id' => $produkStok->produk_id,
            'detail_id' => $detail_id,
        ]);

        $produkStok->update([
            'status' => 'manual',
        ]);

        return redirect()->route('produk.stok', ['produk' => $produkStok->produk_id]);
    }
}
