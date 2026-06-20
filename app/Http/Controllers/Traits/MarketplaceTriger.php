<?php

namespace App\Http\Controllers\Traits;

use App\Services\StokService;
use Illuminate\Support\Facades\DB;

trait MarketplaceTriger
{
    use ShopeeApi;

    public function mpBeli($sku, $marketplace, $jumlah, $id)
    {
        $projectMp = DB::table('project_mps')->find($id);
        $nota = $projectMp->nota ?? '';

        app(StokService::class)->mpBeli(
            $sku,
            $jumlah,
            'dibeli ' . $marketplace->nama . '(' . $nota . ')',
            $id
        );
    }

    public function getLastStok($produk_id)
    {
        return app(StokService::class)->saldoTersedia($produk_id);
    }

    public function updateLastStok($produk_id, $saldo = null)
    {
        app(StokService::class)->updateLastStok($produk_id);
    }

    public function updateStokMp($produk_id)
    {
        $this->updateLastStok($produk_id);
    }
}
