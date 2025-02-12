<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HutangDetail extends Model
{
    protected $fillable = [
        'hutang_id',
        'akun_detail_id',
        'tanggal',
        'jumlah',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function hutang()
    {
        return $this->belongsTo(Hutang::class);
    }

    public function akun_detail()
    {
        return $this->belongsTo(AkunDetail::class);
    }
}
