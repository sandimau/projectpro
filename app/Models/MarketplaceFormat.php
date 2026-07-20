<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceFormat extends Model
{
    public $table = 'marketplace_formats';

    public static function shopee()
    {
        return self::where('marketplace', 'shopee')->where('jenis', 'order')->first();
    }
}
