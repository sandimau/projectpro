<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produksi extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'produksis';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'nama',
        'grup',
        'warna',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public static function ambilFlow($grup)
    {
        return self::where('nama', $grup)->first()->id;
    }
}
