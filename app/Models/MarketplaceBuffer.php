<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceBuffer extends Model
{
    use HasFactory;

    public $table = 'marketplace_buffers';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function projectMp()
    {
        return $this->belongsTo(ProjectMp::class, 'project_id');
    }
}
