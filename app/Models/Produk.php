<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use SoftDeletes;

    public $table = 'produks';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    public function getLastStokAttribute()
    {
        if ($this->attributes['stok'] == 1) {
            $stok = $this->lastStok()->first();
            if ($stok) {
                return $stok->pivot->saldo;
            } else {
                return 0;
            }
        } else {
            return '';
        }
    }

    public function akunDetail()
    {
        return $this->belongsTo(AkunDetail::class, 'akun_detail_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function lastStok()
    {
        return $this->belongsToMany(Produk::class, 'produk_last_stoks', 'produk_id')->withPivot('saldo');
    }
}
