<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ProdukStok;
use App\Models\ProdukModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProdukController extends Controller
{
    public function create(Request $request)
    {
        $produkModel = ProdukModel::find($request->produkModel);
        return view('produk.create', compact('produkModel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'status' => 'required|in:0,1',
            'produk_model_id' => 'required|exists:produk_models,id'
        ]);

        Produk::create([
            'nama' => $request->nama,
            'status' => $request->status,
            'produk_model_id' => $request->produk_model_id,
        ]);

        $produkModel = ProdukModel::find($request->produk_model_id);
        return redirect()->route('produkModel.show', ['produkModel' => $produkModel->id, 'kategori_id' => $produkModel->kategori_id])->with('success', 'Produk berhasil ditambahkan');
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

    public function stok(Produk $produk)
    {
        $produks = ProdukStok::where('produk_id', $produk->id)->orderBy('created_at', 'desc')->get();
        return view('produk.stok', compact('produks'));
    }

    public function aset()
    {
        $asets = DB::table('produk_last_stoks as t')
            ->join(
                DB::raw('(SELECT produk_id FROM produk_last_stoks GROUP BY produk_id) as subquery'),
                't.produk_id',
                '=',
                'subquery.produk_id'
            )
            ->join('produks as p', 'p.id', '=', 't.produk_id')
            ->join('produk_models as pm', 'pm.id', '=', 'p.produk_model_id')
            ->join('produk_kategoris as k', 'k.id', '=', 'pm.kategori_id')
            ->join('produk_kategori_utamas as ku', 'ku.id', '=', 'k.kategori_utama_id')
            ->select(
                't.saldo',
                'pm.harga',
                'pm.nama as namaProdukModel',
                'p.nama as namaProduk',
                'ku.nama as namaKategoriUtama',
                'k.nama as namaKategori',
                'k.id as kategori_id'
            )
            ->orderBy('ku.nama')
            ->orderBy('k.nama')
            ->orderBy('pm.nama')
            ->get();

        return view('admin.produks.aset', compact('asets'));
    }

    public function asetDetail(Kategori $kategori)
    {
        $asets = DB::table('produk_last_stoks as t')
            ->join('produks as p', 'p.id', '=', 't.produk_id')
            ->join('produk_models as pm', 'pm.id', '=', 'p.produk_model_id')
            ->join('produk_kategoris as k', 'k.id', '=', 'pm.kategori_id')
            ->where('pm.kategori_id', $kategori->id)
            ->select(
                DB::raw("CONCAT(k.nama, ' - ', pm.nama) as namaProduk"),
                'p.nama as varian',
                't.saldo as stok',
                'pm.harga',
                DB::raw('t.saldo * pm.harga as nilai_aset')
            )
            ->orderBy('p.nama')
            ->get();

        return view('admin.produks.asetDetail', compact('asets', 'kategori'));
    }
}
