<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKategoriUtama extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = ['nama', 'jual', 'beli', 'stok', 'produksi'];

    protected $casts = [
        'jual' => 'boolean',
        'beli' => 'boolean',
        'stok' => 'boolean',
        'produksi' => 'boolean',
    ];
}
