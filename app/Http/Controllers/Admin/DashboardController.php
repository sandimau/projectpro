<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [];

        // === OMZET OFFLINE PEKANAN (7 hari terakhir) ===
        $data['omzetOffline'] = $this->getOmzetOfflinePekanan();

        // === OMZET ONLINE PEKANAN (7 hari terakhir) ===
        $data['omzetOnline'] = $this->getOmzetOnlinePekanan();

        // === ORDER TERBESAR OFFLINE PEKANAN ===
        $data['orderTerbesarOffline'] = Order::select('id', 'total', 'created_at', 'kontak_id')
            ->with('kontak:id,nama')
            ->whereNull('marketplace')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // === PENJUALAN TERBAIK PEKANAN ===
        $data['produkTerlaris'] = $this->getProdukTerlarisPekanan();

        // === ORDER TERBESAR HARI INI ===
        $data['orderTerbesarHariIni'] = $this->getOrderTerbesarHariIni();

        // === Marketplace List untuk chart ===
        $data['marketplaces'] = Marketplace::pluck('nama', 'id');
        return view('admin.dashboard.index', $data);
    }

    /**
     * Ambil omzet offline per hari (7 hari terakhir)
     */
    private function getOmzetOfflinePekanan()
    {
        $results = DB::select("
            SELECT
                DATE(created_at) as date,
                SUM(total) as total_omzet
            FROM orders
            WHERE marketplace IS NULL
            AND deleted_at IS NULL
            AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at)
        ");

        // Format data untuk chart
        $dateRange = collect(range(0, 6))->map(function ($day) {
            return now()->subDays(6 - $day)->format('Y-m-d');
        });

        $data = collect();
        foreach ($dateRange as $date) {
            $found = collect($results)->firstWhere('date', $date);
            $data[$date] = (object)[
                'date' => $date,
                'offline' => $found ? $found->total_omzet : 0
            ];
        }

        return $data;
    }

    /**
     * Ambil omzet online per marketplace per hari (7 hari terakhir)
     */
    private function getOmzetOnlinePekanan()
    {
        $results = DB::select("
            SELECT
                m.id as marketplace_id,
                m.nama as marketplace_nama,
                DATE(o.created_at) as date,
                COALESCE(SUM(o.total), 0) as total_omzet
            FROM marketplaces m
            LEFT JOIN project_mps o ON m.id = o.marketplace_id
                AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY m.id, m.nama, DATE(o.created_at)
            ORDER BY m.id, DATE(o.created_at)
        ");

        // Format data untuk chart
        $dateRange = collect(range(0, 6))->map(function ($day) {
            return now()->subDays(6 - $day)->format('Y-m-d');
        });

        $data = collect();
        foreach ($dateRange as $date) {
            $data[$date] = (object)['date' => $date];
        }

        // Populate data per marketplace
        foreach ($results as $result) {
            if ($result->date && isset($data[$result->date])) {
                $columnName = str_replace(' ', '_', substr($result->marketplace_nama, 0, 13));
                if (!isset($data[$result->date]->$columnName)) {
                    $data[$result->date]->$columnName = 0;
                }
                $data[$result->date]->$columnName += $result->total_omzet;
            }
        }

        return $data;
    }

    /**
     * Ambil produk terlaris pekanan
     */
    private function getProdukTerlarisPekanan()
    {
        $results = DB::select("
            SELECT
                p.id as produk_id,
                p.nama as nama_produk,
                pm.nama as model_nama,
                pk.nama as kategori_nama,
                SUM(pmd.jumlah) as total,
                SUM(pmd.jumlah * pmd.harga) as omzet
            FROM project_mp_details pmd
            INNER JOIN project_mps o ON pmd.project_id = o.id
            INNER JOIN produks p ON pmd.produk_id = p.id
            INNER JOIN produk_models pm ON p.produk_model_id = pm.id
            INNER JOIN produk_kategoris pk ON pm.kategori_id = pk.id
            WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY p.id, p.nama, pm.nama, pk.nama
            ORDER BY omzet DESC
            LIMIT 10
        ");

        return collect($results)->map(function ($item) {
            $nama = ($item->model_nama ?? '') . (!empty($item->nama_produk) ? ' (' . $item->nama_produk . ')' : '');

            return [
                'nama_produk' => $nama,
                'total' => (int)$item->total,
                'omzet' => (int)($item->omzet ?? 0)
            ];
        });
    }

    /**
     * Ambil order terbesar hari ini
     */
    private function getOrderTerbesarHariIni()
    {
        $results = DB::select("
            SELECT
                p.id as produk_id,
                p.nama as nama_produk,
                pm.nama as model_nama,
                pk.nama as kategori_nama,
                SUM(pmd.jumlah) as total,
                SUM(pmd.jumlah * pmd.harga) as omzet
            FROM project_mp_details pmd
            INNER JOIN project_mps o ON pmd.project_id = o.id
            INNER JOIN produks p ON pmd.produk_id = p.id
            INNER JOIN produk_models pm ON p.produk_model_id = pm.id
            INNER JOIN produk_kategoris pk ON pm.kategori_id = pk.id
            WHERE DATE(o.created_at) = CURDATE()
            GROUP BY p.id, p.nama, pm.nama, pk.nama
            ORDER BY omzet DESC
            LIMIT 10
        ");

        return collect($results)->map(function ($item) {
            $nama = ($item->model_nama ?? '') . (!empty($item->nama_produk) ? ' (' . $item->nama_produk . ')' : '');

            return [
                'nama_produk' => $nama,
                'total' => (int)$item->total,
                'omzet' => (int)($item->omzet ?? 0)
            ];
        });
    }
}

