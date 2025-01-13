<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdukStok extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'produk_stoks';

    protected $dates = [
        'tanggal',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        ProdukStok::saving(function ($model) {

            $terakhir = ($model->where('produk_id', $model->produk_id)->latest('id')->first()->saldo) ?? 0;
            $model->saldo = $terakhir + $model->tambah - $model->kurang;

            $dataProduk = Produk::find($model->produk_id)->lastStok()->where('produk_id',$model->produk_id)->latest('id')->first();
            if ($dataProduk) {
                $dataProduk->lastStok()->updateExistingPivot($model->produk_id, [
                    'saldo' => $model->saldo,
                ]);
            } else {
                $dataProduk = Produk::find($model->produk_id);
                $dataProduk->lastStok()->attach($model->produk_id, ['saldo' => $model->saldo]);
            }

        });
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    static function lastStok($produk)
    {
        return self::where('produk_id', $produk)->orderBy('id', 'desc')->first()->saldo ?? 0;
    }
}
