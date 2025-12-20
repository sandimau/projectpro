<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukProduksi extends Model
{
    use HasFactory;

    public $table = 'produk_produksis';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'produk_id',
        'satuan',
        'panjang',
        'lebar',
        'perbandingan',
        'user_id',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id')->with('produkModel');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
