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
            $model->saldo = (int)$terakhir + (int)$model->tambah - (int)$model->kurang;

            $dataProduk = Produk::find($model->produk_id);

            if ($dataProduk) {
                $existingStok = $dataProduk->lastStok()->where('produk_id', $model->produk_id)->latest('id')->first();

                if ($existingStok) {
                    $dataProduk->lastStok()->updateExistingPivot($model->produk_id, [
                        'saldo' => $model->saldo,
                    ]);
                } else {
                    $dataProduk->lastStok()->attach($model->produk_id, ['saldo' => $model->saldo]);
                }
            }

            $model->hpp = $model->produk->hpp ?? 0;
            $model->user_id = auth()->user()->id;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
