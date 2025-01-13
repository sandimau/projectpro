<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Sistem extends Model
{
    public $table = 'sistems';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];
}
