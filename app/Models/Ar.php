<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ar extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'ars';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
