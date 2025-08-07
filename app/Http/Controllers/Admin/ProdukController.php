<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ProdukStok;
use App\Models\ProdukModel;
use Illuminate\Http\Request;
use App\Models\BelanjaDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProdukController extends Controller
{
    public function create(Request $request)
    {
        $produkModel = ProdukModel::find($request->produkModel);
        return view('admin.produks.create', compact('produkModel'));
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
        $produkModel = ProdukModel::find($produk->produk_model_id);
        return view('admin.produks.edit', compact('produk', 'produkModel'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama' => 'required',
            'status' => 'required|in:0,1',
        ]);

        $produkModel = ProdukModel::find($produk->produk_model_id);

        $produk->update($request->all());
        return redirect()->route('produkModel.show', ['produkModel' => $produkModel->id])->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }

    public function stok(Produk $produk)
    {
        $produkStoks = ProdukStok::where('produk_id', $produk->id)->orderBy('id', 'desc')->get();
        return view('admin.produkStoks.index', compact('produkStoks','produk'));
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
                'k.id as kategori_id',
                'ku.nama as namaKategoriUtama',
                'k.nama as namaKategori',
                DB::raw('SUM(t.saldo * pm.harga) as nilai_aset')
            )
            ->groupBy('k.id', 'ku.nama', 'k.nama')
            ->orderBy('ku.nama')
            ->orderBy('k.nama')
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

    public function omzet(Request $request)
    {
        // Get selected year, default to current year if not specified
        $selectedYear = $request->input('year', date('Y'));

        // Get all available years for the dropdown
        $years = DB::table('orders')
            ->select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

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
                'k.id as kategori_id',
                'ku.nama as namaKategoriUtama',
                'k.nama as namaKategori',
                DB::raw('SUM(t.saldo * pm.harga) as nilai_aset')
            )
            ->groupBy('k.id', 'ku.nama', 'k.nama')
            ->orderBy('ku.nama')
            ->orderBy('k.nama')
            ->get();

        // Get categories first
        $categories = DB::table('produk_kategoris as k')
            ->join('produk_kategori_utamas as ku', 'ku.id', '=', 'k.kategori_utama_id')
            ->select(
                'k.id as kategori_id',
                'ku.nama as namaKategoriUtama',
                'k.nama as namaKategori'
            )
            ->where('ku.jual', 1)
            ->orderBy('ku.nama')
            ->orderBy('k.nama')
            ->get();

        // Get produksi batal ID for exclusion
        $batalProduksiId = DB::table('produksis')->where('nama', 'batal')->first()->id ?? null;

        // Get omzet data for the selected year
        $omzetData = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('produks as p', 'p.id', '=', 'od.produk_id')
            ->join('produk_models as pm', 'pm.id', '=', 'p.produk_model_id')
            ->join('produk_kategoris as k', 'k.id', '=', 'pm.kategori_id')
            ->join('produk_kategori_utamas as ku', 'ku.id', '=', 'k.kategori_utama_id')
            ->whereYear('o.created_at', $selectedYear)
            ->when($batalProduksiId, function($query) use ($batalProduksiId) {
                return $query->where('od.produksi_id', '!=', $batalProduksiId);
            })
            ->select(
                'k.id as kategori_id',
                'ku.nama as namaKategoriUtama',
                'k.nama as namaKategori',
                DB::raw('MONTH(o.created_at) as bulan'),
                DB::raw('SUM(od.jumlah * od.harga) as omzet')
            )
            ->groupBy('k.id', 'ku.nama', 'k.nama', DB::raw('MONTH(o.created_at)'))
            ->get();

        // Create complete dataset with all months
        $omzet = collect();
        foreach ($categories as $category) {
            for ($month = 1; $month <= 12; $month++) {
                $monthlyData = $omzetData
                    ->where('kategori_id', $category->kategori_id)
                    ->where('bulan', $month)
                    ->first();

                $omzet->push((object)[
                    'kategori_id' => $category->kategori_id,
                    'namaKategoriUtama' => $category->namaKategoriUtama,
                    'namaKategori' => $category->namaKategori,
                    'bulan' => $month,
                    'tahun' => $selectedYear,
                    'omzet' => $monthlyData ? $monthlyData->omzet : 0
                ]);
            }
        }

        return view('admin.produks.omzet', compact('omzet', 'asets', 'years', 'selectedYear'));
    }

    public function omzetDetail(Kategori $kategori, Request $request)
    {
        // Get selected year and month, default to current if not specified
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('m'));

        // Get all available years for the dropdown
        $years = DB::table('orders')
            ->select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Get products with their daily sales for the selected month
        $products = DB::table('produks as p')
            ->join('produk_models as pm', 'pm.id', '=', 'p.produk_model_id')
            ->leftJoin('produk_last_stoks as pls', 'pls.produk_id', '=', 'p.id')
            ->where('pm.kategori_id', $kategori->id)
            ->select(
                'p.id',
                DB::raw("CONCAT(pm.nama) as nama_produk"),
                'p.nama as varian',
                'pls.saldo as stok'
            )
            ->get();

        // Get daily sales data
        foreach ($products as $product) {
            // Calculate average sales for the last 3 months
            $avgSales = DB::table('order_details as od')
                ->join('orders as o', 'o.id', '=', 'od.order_id')
                ->where('od.produk_id', $product->id)
                ->whereRaw('o.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)')
                ->avg('od.jumlah');

            $product->rata_penjualan = round($avgSales ?: 0, 1);

            // Get daily sales for the selected month
            $dailySales = DB::table('order_details as od')
                ->join('orders as o', 'o.id', '=', 'od.order_id')
                ->where('od.produk_id', $product->id)
                ->whereYear('o.created_at', $selectedYear)
                ->whereMonth('o.created_at', $selectedMonth)
                ->select(
                    DB::raw('DAY(o.created_at) as day'),
                    DB::raw('SUM(od.jumlah) as total_sales')
                )
                ->groupBy(DB::raw('DAY(o.created_at)'))
                ->get()
                ->pluck('total_sales', 'day')
                ->toArray();

            $product->daily_sales = $dailySales;
        }

        return view('admin.produks.omzetDetail', compact('products', 'kategori', 'years', 'selectedYear', 'selectedMonth'));
    }

    public function belanja(Produk $produk)
    {
        $belanjas = BelanjaDetail::where('produk_id', $produk->id)->orderBy('id', 'desc')->limit(30)->get();
        return view('admin.produks.belanja', compact('belanjas', 'produk'));
    }
}
