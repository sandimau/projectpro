<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceLog extends Model
{
    use HasFactory;

    public $table = 'marketplace_logs';

    public $timestamps = false;

    protected $guarded = [];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class, 'shop_id', 'shop_id');
    }
}
