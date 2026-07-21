<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use Illuminate\Database\Eloquent\Model;

class HutangDetail extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'hutang_id',
        'akun_detail_id',
        'tanggal',
        'jumlah',
        'keterangan',
        'user_id'
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
