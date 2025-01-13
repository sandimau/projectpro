<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use SoftDeletes;

    public $table = 'pembayarans';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function scopeApprove($query)
    {
        $query->where('status', 'approve');
        return $query;
    }

    public function akunDetail()
    {
        return $this->belongsTo(AkunDetail::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
