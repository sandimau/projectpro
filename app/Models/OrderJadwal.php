<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orderjadwal extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'orderjadwals';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function order_detail()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id');
    }

    public function spek()
    {
        return $this->belongsTo(Spek::class, 'spek_id');
    }
}
