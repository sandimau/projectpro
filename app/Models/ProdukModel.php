<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdukModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'harga',
        'satuan',
        'deskripsi',
        'jual',
        'beli',
        'stok',
        'gambar',
        'kategori_id',
        'kontak_id'
    ];

    public function kategori()
    {
        return $this->belongsTo(ProdukKategori::class, 'kategori_id');
    }

    public function kontak()
    {
        return $this->belongsTo(Kontak::class, 'kontak_id');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'produk_model_id');
    }
}
