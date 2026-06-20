<?php

namespace App\Models;

use App\Services\StokService;
use Illuminate\Database\Eloquent\Model;

class ProdukLastStok extends Model
{
    public $table = 'produk_last_stoks';

    protected $guarded = [];

    public static function latestPerProdukSubquery(): string
    {
        return "(
            SELECT pls.produk_id, pls.saldo, pls.tahun
            FROM produk_last_stoks pls
            INNER JOIN (
                SELECT produk_id, MAX(tahun) as max_tahun
                FROM produk_last_stoks
                GROUP BY produk_id
            ) sub ON pls.produk_id = sub.produk_id AND pls.tahun = sub.max_tahun
        )";
    }

    public static function stok($produk_id): int
    {
        return app(StokService::class)->saldoTersedia($produk_id);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
