<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukProduksiHasil extends Model
{
    use HasFactory;

    public $table = 'produk_produksi_hasils';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'produk_id',
        'produksi_id',
        'jumlah',
        'hpp',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->user_id = auth()->user()->id;
        });

        self::saved(function ($model) {
            $model->produksi->hitungHpp();
        });

        self::deleted(function ($model) {
            $model->produksi->hitungHpp();
        });
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id')->with('produkModel');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produksi()
    {
        return $this->belongsTo(ProduksiProduk::class, 'produksi_id');
    }

    public function produkProduksi()
    {
        return $this->belongsTo(ProdukProduksi::class, 'produk_id', 'produk_id');
    }

    public function getPerbandinganAttribute()
    {
        return $this->produkProduksi->perbandingan;
    }

    public function getSatuanAttribute()
    {
        return $this->produkProduksi->satuan;
    }
}
