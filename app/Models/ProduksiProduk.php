<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ProduksiProduk extends Model
{
    public $table = 'produksi_produks';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function getUserAttribute()
    {
        $user = User::find(($this->attributes['user_id'])??0);

        if($user) {
            return substr($user->email, 0, 5);
        } else {
            return null;
        }
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id')->with('produkModel');
    }

    public function belanja()
    {
        return $this->belongsToMany(Belanja::class, 'produk_produksi_belanja', 'produksi_id', 'belanja_id');
    }

    public function hitungBiaya()
    {
        $biaya = $this->belanja()->sum('total');
        $stok = $this->bahan()->sum(DB::raw('jumlah * hpp'));

        $this->update(['biaya' => $biaya + $stok]);
    }

    public function bahan()
    {
        return $this->hasMany(ProduksiBahan::class, 'produksi_id');
    }
}
