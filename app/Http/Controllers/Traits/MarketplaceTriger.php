<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\DB;

trait MarketplaceTriger
{
    use ShopeeApi;

    public function mpBeli($sku, $marketplace, $jumlah, $id)
    {
        $projectMp = DB::table('project_mps')->find($id);
        $nota = $projectMp->nota ?? '';

        // Ambil saldo terakhir
        $lastStok = $this->getLastStok($sku);
        $saldo = $lastStok - $jumlah;

        DB::table('produk_stoks')->insert([
            'produk_id' => $sku,
            'kurang' => $jumlah,
            'tambah' => 0,
            'saldo' => $saldo,
            'keterangan' => 'dibeli ' . $marketplace->nama . '(' . $nota . ')',
            'kode' => 'shp',
            'created_at' => now(),
            'detail_id' => $id
        ]);

        $this->updateLastStok($sku, $saldo);
    }

    public function getLastStok($produk_id)
    {
        return DB::table('produk_stoks')
            ->where('produk_id', $produk_id)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->first()->saldo ?? 0;
    }

    public function updateLastStok($produk_id, $saldo = null)
    {
        if ($saldo === null) {
            $saldo = $this->getLastStok($produk_id);
        }

        DB::table('produk_last_stoks')->updateOrInsert(
            ['produk_id' => $produk_id],
            ['saldo' => $saldo, 'updated_at' => now()]
        );
    }

    public function updateStokMp($produk_id)
    {
        $this->updateLastStok($produk_id);
    }
}
