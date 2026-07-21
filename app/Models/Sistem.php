<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Sistem extends Model
{
    use BelongsToCompany;

    public $table = 'sistems';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];
}
