<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMp extends Model
{
    use HasFactory;

    public $table = 'project_mps';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function details()
    {
        return $this->hasMany(ProjectMpDetail::class, 'project_id');
    }
}
