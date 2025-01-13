<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tunjangan extends Model
{
    use HasFactory;

    public $table = 'tunjangans';
    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function akunDetail()
    {
        return $this->belongsTo(AkunDetail::class, 'akun_detail_id');
    }

}
