<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Whattodo extends Model
{
    public $table = 'whattodos';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];
}
