<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukMarketplace extends Model
{
    use HasFactory;

    public $table = 'produk_marketplaces';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}
