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
}
