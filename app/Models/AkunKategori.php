<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AkunKategori extends Model
{
    use BelongsToCompany, SoftDeletes, HasFactory;

    public $table = 'akun_kategoris';

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

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }
}
