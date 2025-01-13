<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BelanjaDetail extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'belanja_details';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    public function belanja()
    {
        return $this->belongsTo(Belanja::class, 'belanja_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
