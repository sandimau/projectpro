<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lembur extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'lemburs';

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

    public function getBulanAttribute()
    {
        $bulan = [
            1 => 'januari',
            2 => 'februari',
            3 => 'maret', 'april',
            4 => 'mei',
            5 => 'juni',
            6 => 'juli',
            7 => 'agustus',
            8 => 'september',
            9 => 'oktober',
            10 => 'november',
            11 => 'desember',
        ];

        return $bulan[$this->attributes['bulan']];

    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
