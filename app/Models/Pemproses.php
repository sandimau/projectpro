<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemproses extends Model
{
    use BelongsToCompany, HasFactory;

    public $table = 'pemproses';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'nama',
        'warna',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
