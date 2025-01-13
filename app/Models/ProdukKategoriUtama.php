<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKategoriUtama extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'jual', 'beli', 'stok'];

    protected $casts = [
        'jual' => 'boolean',
        'beli' => 'boolean',
        'stok' => 'boolean',
    ];
}
