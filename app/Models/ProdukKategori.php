<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdukKategori extends Model
{
    use BelongsToCompany, HasFactory, SoftDeletes;

    protected $fillable = ['nama', 'kategori_utama_id'];

    public function kategoriUtama()
    {
        return $this->belongsTo(ProdukKategoriUtama::class, 'kategori_utama_id');
    }
}
