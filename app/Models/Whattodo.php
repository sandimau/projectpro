<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use Illuminate\Database\Eloquent\Model;

class Whattodo extends Model
{
    use BelongsToCompany;

    public $table = 'whattodos';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];
}
