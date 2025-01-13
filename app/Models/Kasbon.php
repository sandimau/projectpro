<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kasbon extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'kasbons';

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

    public function akun_detail()
    {
        return $this->belongsTo(AkunDetail::class, 'akun_detail_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
