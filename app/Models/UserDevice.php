<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDevice extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'user_id',
        'device_hash',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
