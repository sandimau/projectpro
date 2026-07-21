<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use BelongsToCompany, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
