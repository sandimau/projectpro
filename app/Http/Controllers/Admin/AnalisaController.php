<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Akun;
use App\Models\AkunKategori;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Penggajian;
use App\Models\Tunjangan;
use App\Models\Belanja;
use App\Models\BelanjaDetail;
use App\Models\BukuBesar;
use App\Models\ProdukStok;
use App\Models\Produk;
use App\Models\ProdukModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AnalisaController extends Controller
{
    public function beban()
    {
        return view('admin.analisa.beban');
    }

    public function getDataBeban(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $data = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            // Tunjangan
            $tunjangan = Tunjangan::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->sum('jumlah') ?? 0;

            // Beban Operasional (Belanja non-stok)
            $operasional = DB::table('produks')
                ->selectRaw('sum(belanja_details.harga*jumlah) as total')
                ->join('produk_models', 'produk_model_id', '=', 'produk_models.id')
                ->join('belanja_details', 'produk_id', '=', 'produks.id')
                ->join('belanjas', 'belanja_details.belanja_id', '=', 'belanjas.id')
                ->whereYear('belanjas.created_at', $tahun)
                ->whereMonth('belanjas.created_at', $bulan)
                ->whereNull('produk_models.stok')
                ->first()->total ?? 0;

            // Penggajian
            $penggajian = Penggajian::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->sum('total') ?? 0;

            // Pemakaian Stok (produk_stoks dengan kurang)
            $pemakaianStok = ProdukStok::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->where('keterangan', 'like', '%pakai%')
                ->selectRaw('SUM(COALESCE(hpp, 0) * COALESCE(kurang, 0)) as total')
                ->value('total') ?? 0;

            $data[] = [
                'bulan' => $bulan,
                'nama_bulan' => $this->getNamaBulan($bulan),
                'operasional' => (float) $operasional,
                'penggajian' => (float) $penggajian,
                'tunjangan' => (float) $tunjangan,
                'pemakaian_stok' => (float) $pemakaianStok,
                'total' => (float) ($operasional + $penggajian + $tunjangan + $pemakaianStok)
            ];
        }

        return response()->json($data);
    }

    private function getNamaBulan($bulan)
    {
        $namaBulan = [
            1 => 'januari', 2 => 'februari', 3 => 'maret', 4 => 'april',
            5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'agustus',
            9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember'
        ];
        return $namaBulan[$bulan];
    }

    public function operasional()
    {
        return view('admin.analisa.operasional');
    }

    public function stok()
    {
        return view('admin.analisa.stok');
    }
}
