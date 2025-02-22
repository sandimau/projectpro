<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Hutang;
use App\Models\Produk;
use App\Models\Tunjangan;
use App\Models\AkunDetail;
use App\Models\Penggajian;
use App\Models\ProdukStok;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class LaporanController extends Controller
{
    public function neraca()
    {
        $kas = AkunDetail::TotalKas();
        $modal = AkunDetail::modal();

        $piutang = Hutang::with('details')->where('jenis', '=', 'piutang')->get();
        $hutang = Hutang::with('details')->where('jenis', '=', 'hutang')->get();

        $total_piutang = 0;
        $total_hutang = 0;
        $total_order = 0;
        foreach ($piutang as $item) {
            $total_piutang += $item->sisa;
        }

        foreach ($hutang as $item) {
            $total_hutang += $item->sisa;
        }

        $order = Order::whereNull('marketplace')->get();
        $total_order = 0;
        foreach ($order as $item) {
            $total_order += $item->kekurangan;
        }

        $produk = Produk::all();

        $stok = 0;
        foreach ($produk as $item) {
            $stok += ProdukStok::lastStok($item->id) * $item->produkModel->harga;
        }

        return view('admin.laporan.neraca', compact('kas', 'modal', 'total_piutang', 'total_hutang', 'stok', 'total_order'));
    }

    public function labarugi()
    {
        $bulan = request('bulan') ?? date('Y-m');
        $pilihan_parts = explode('-', $bulan);
        $thn = $pilihan_parts[0];
        $bln = $pilihan_parts[1];

        $potongan = Order::selectRaw('sum(ongkir-diskon) as total_omzet')->whereYear('created_at', $thn)->whereMonth('created_at', $bln)->first()->total_omzet;

        $penjualan = DB::table('order_details')
            ->selectRaw('sum(jumlah*harga) as total_omzet,sum(hpp * jumlah) as total_hpp')
            ->join('produksis', 'produksi_id', '=', 'produksis.id')
            ->join('orders', 'order_id', '=', 'orders.id')
            ->where('produksis.id', '<>', 4)
            ->whereYear('orders.created_at', $thn)
            ->whereMonth('orders.created_at', $bln)
            ->first();

        $opname = ProdukStok::selectRaw('sum(hpp * COALESCE(tambah,0) - hpp * COALESCE(kurang,0)) as total_opname')
            ->where('kode', 'opn')
            ->whereYear('created_at', $thn)
            ->whereMonth('created_at', $bln)
            ->first()->total_opname;

        $beban = DB::table('produks')
            ->selectRaw('sum(belanja_details.harga*jumlah) as total,
            produk_kategoris.nama as kategori,kategori_id')
            ->join('produk_models', 'produk_model_id', '=', 'produk_models.id')
            ->join('belanja_details', 'produk_id', '=', 'produks.id')
            ->join('belanjas', 'belanja_details.belanja_id', '=', 'belanjas.id')
            ->join('produk_kategoris', 'produk_kategoris.id', '=', 'kategori_id')
            ->join('produk_kategori_utamas', 'produk_kategori_utamas.id', '=', 'kategori_utama_id')
            ->whereYear('belanjas.created_at', $thn)
            ->whereMonth('belanjas.created_at', $bln)
            ->first()->total;

        $gaji = Penggajian::selectRaw('sum(total+kasbon) as total_gaji')
            ->whereYear('created_at', $thn)
            ->whereMonth('created_at', $bln)
            ->first()->total_gaji;

        $tunjangan = Tunjangan::selectRaw('sum(jumlah) as total_tunjangan')
            ->whereYear('created_at', $thn)
            ->whereMonth('created_at', $bln)
            ->first()->total_tunjangan;

        $omzet = $penjualan->total_omzet + $potongan;
        $hpp = $penjualan->total_hpp;

        $bulan = [];
        $tahun_skr = date('Y');
        $bulan_skr = date('n');

        for ($i = 1; $i <= $bulan_skr; $i++) {
            $bulan[$tahun_skr . '-' . str_pad($i, 2, '0', STR_PAD_LEFT)] = date('F', mktime(0, 0, 0, $i, 1)) . ' ' . $tahun_skr;
        }

        return view('admin.laporan.labarugi', compact('omzet', 'hpp', 'opname', 'beban', 'gaji', 'tunjangan', 'bulan'));
    }

    public function labaKotor(Request $request)
    {
        $bulan = $request->bulan ?? date('Y-m');
        $pilihan_parts = explode('-', $bulan);
        $thn = $pilihan_parts[0];
        $bln = $pilihan_parts[1];
        $view_type = $request->view_type ?? 'kategori';

        if ($view_type == 'kategori') {
            // Get data per kategori
            $data = DB::table('order_details as od')
                ->join('orders as o', 'o.id', '=', 'od.order_id')
                ->join('produks as p', 'p.id', '=', 'od.produk_id')
                ->join('produk_models as pm', 'pm.id', '=', 'p.produk_model_id')
                ->join('produk_kategoris as pk', 'pk.id', '=', 'pm.kategori_id')
                ->join('produk_kategori_utamas as pku', 'pku.id', '=', 'pk.kategori_utama_id')
                ->where('od.produksi_id', '<>', 4)
                ->whereYear('o.created_at', $thn)
                ->whereMonth('o.created_at', $bln)
                ->select(
                    'pku.nama as kategori_utama',
                    'pk.nama as kategori',
                    DB::raw('SUM(od.jumlah * od.harga) as omzet'),
                    DB::raw('SUM(od.hpp * od.jumlah) as hpp'),
                    DB::raw('COALESCE((
                        SELECT SUM(ps.hpp * COALESCE(ps.tambah,0) - ps.hpp * COALESCE(ps.kurang,0))
                        FROM produk_stoks ps
                        JOIN produks p2 ON p2.id = ps.produk_id
                        JOIN produk_models pm2 ON pm2.id = p2.produk_model_id
                        JOIN produk_kategoris pk2 ON pk2.id = pm2.kategori_id
                        WHERE ps.kode = "opn"
                        AND pk2.id = pk.id
                        AND YEAR(ps.created_at) = ' . $thn . '
                        AND MONTH(ps.created_at) = ' . $bln . '
                    ), 0) as opname'),
                    DB::raw('(
                        SUM(od.jumlah * od.harga) -
                        SUM(od.hpp * od.jumlah) +
                        COALESCE((
                            SELECT SUM(ps.hpp * COALESCE(ps.tambah,0) - ps.hpp * COALESCE(ps.kurang,0))
                            FROM produk_stoks ps
                            JOIN produks p2 ON p2.id = ps.produk_id
                            JOIN produk_models pm2 ON pm2.id = p2.produk_model_id
                            JOIN produk_kategoris pk2 ON pk2.id = pm2.kategori_id
                            WHERE ps.kode = "opn"
                            AND pk2.id = pk.id
                            AND YEAR(ps.created_at) = ' . $thn . '
                            AND MONTH(ps.created_at) = ' . $bln . '
                        ), 0)
                    ) as laba_kotor'),
                    DB::raw('CASE
                        WHEN SUM(od.jumlah * od.harga) > 0
                        THEN ((SUM(od.jumlah * od.harga) - SUM(od.hpp * od.jumlah)) / SUM(od.jumlah * od.harga)) * 100
                        ELSE 0
                    END as persen')
                )
                ->groupBy('pku.nama', 'pk.nama')
                ->orderBy('pku.nama')
                ->orderBy('pk.nama')
                ->get();
        } else {
            // Get data per produk
            $data = DB::table('order_details as od')
                ->join('orders as o', 'o.id', '=', 'od.order_id')
                ->join('produks as p', 'p.id', '=', 'od.produk_id')
                ->join('produk_models as pm', 'pm.id', '=', 'p.produk_model_id')
                ->join('produk_kategoris as pk', 'pk.id', '=', 'pm.kategori_id')
                ->join('produk_kategori_utamas as pku', 'pku.id', '=', 'pk.kategori_utama_id')
                ->where('od.produksi_id', '<>', 4)
                ->whereYear('o.created_at', $thn)
                ->whereMonth('o.created_at', $bln)
                ->select(
                    'pku.nama as kategori_utama',
                    'pk.nama as kategori',
                    'p.nama as produk',
                    DB::raw('SUM(od.jumlah * od.harga) as omzet'),
                    DB::raw('SUM(od.hpp * od.jumlah) as hpp'),
                    DB::raw('COALESCE((
                        SELECT sum(hpp * COALESCE(tambah,0) - hpp * COALESCE(kurang,0))
                        FROM produk_stoks ps
                        WHERE ps.kode = "opn"
                        AND ps.produk_id = p.id
                        AND YEAR(ps.created_at) = ' . $thn . '
                        AND MONTH(ps.created_at) = ' . $bln . '
                    ), 0) as opname'),
                    DB::raw('(
                        SUM(od.jumlah * od.harga) -
                        SUM(od.hpp * od.jumlah) +
                        COALESCE((
                            SELECT sum(hpp * COALESCE(tambah,0) - hpp * COALESCE(kurang,0))
                            FROM produk_stoks ps
                            WHERE ps.kode = "opn"
                            AND ps.produk_id = p.id
                            AND YEAR(ps.created_at) = ' . $thn . '
                            AND MONTH(ps.created_at) = ' . $bln . '
                        ), 0)
                    ) as laba_kotor'),
                    DB::raw('CASE
                        WHEN SUM(od.jumlah * od.harga) > 0
                        THEN ((SUM(od.jumlah * od.harga) - SUM(od.hpp * od.jumlah)) / SUM(od.jumlah * od.harga)) * 100
                        ELSE 0
                    END as persen')
                )
                ->groupBy('pku.nama', 'pk.nama', 'p.nama', 'p.id')
                ->orderBy('pku.nama')
                ->orderBy('pk.nama')
                ->orderBy('p.nama')
                ->get();
        }

        // Generate months for dropdown
        $bulanList = [];
        $start_date = now()->startOfYear();
        $end_date = now();

        while ($start_date <= $end_date) {
            $key = $start_date->format('Y-m');
            $bulanList[$key] = $start_date->format('F Y');
            $start_date->addMonth();
        }

        return view('admin.laporan.labakotor', [
            'data' => $data,
            'bulan' => $bulanList,
            'view_type' => $view_type
        ]);
    }
}
