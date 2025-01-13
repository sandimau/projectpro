<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AkunDetail extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'akun_details';

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

    public function akun_kategori()
    {
        return $this->belongsTo(AkunKategori::class, 'akun_kategori_id');
    }
}
