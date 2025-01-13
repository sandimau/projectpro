<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gaji extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'gajis';

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

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function bagian()
    {
        return $this->belongsTo(Bagian::class, 'bagian_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
}
