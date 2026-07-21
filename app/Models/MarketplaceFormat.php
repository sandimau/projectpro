<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use Illuminate\Database\Eloquent\Model;

class MarketplaceFormat extends Model
{
    use BelongsToCompany;

    public $table = 'marketplace_formats';

    public static function shopee()
    {
        return self::where('marketplace', 'shopee')->where('jenis', 'order')->first();
    }
}
