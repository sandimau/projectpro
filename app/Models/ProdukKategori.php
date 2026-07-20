<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdukKategori extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama', 'kategori_utama_id'];

    public function kategoriUtama()
    {
        return $this->belongsTo(ProdukKategoriUtama::class, 'kategori_utama_id');
    }
}
