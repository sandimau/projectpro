<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    use HasFactory;

    public $table = 'marketplaces';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function kontak()
    {
        return $this->belongsTo(Kontak::class, 'kontak_id');
    }

    public function kas()
    {
        return $this->belongsTo(AkunDetail::class, 'kas_id');
    }

    public function kasPenarikan()
    {
        return $this->belongsTo(AkunDetail::class, 'penarikan_id');
    }
}
