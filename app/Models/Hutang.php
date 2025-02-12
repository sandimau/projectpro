<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{
    protected $fillable = [
        'kontak_id',
        'tanggal',
        'jumlah',
        'keterangan',
        'jenis',
        'akun_detail_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jenis' => 'string'
    ];

    public function kontak()
    {
        return $this->belongsTo(Kontak::class);
    }

    public function akun_detail()
    {
        return $this->belongsTo(AkunDetail::class);
    }

    public function details()
    {
        return $this->hasMany(HutangDetail::class);
    }

    public function getTotalBayarAttribute()
    {
        return $this->details->sum('jumlah');
    }

    public function getSisaAttribute()
    {
        return $this->jumlah - $this->total_bayar;
    }

    public function getStatusAttribute()
    {
        return $this->sisa <= 0 ? 'lunas' : 'belum_lunas';
    }
}
