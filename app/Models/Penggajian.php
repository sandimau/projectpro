<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penggajian extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'penggajians';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    public function getBulanAsliAttribute($value)
    {
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $bulan[$this->attributes['bulan']];
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function akun_detail()
    {
        return $this->belongsTo(AkunDetail::class, 'akun_detail_id');
    }
}
