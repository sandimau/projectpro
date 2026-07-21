<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kategori extends Model
{
    use BelongsToCompany;

    public $table = 'produk_kategoris';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function kategoriUtama(): BelongsTo
    {
        return $this->belongsTo(ProdukKategoriUtama::class, 'kategori_utama_id');
    }
}
