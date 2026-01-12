<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkItem extends Model
{
    protected $table = 'link_items';

    protected $fillable = [
        'link_page_id',
        'type',
        'title',
        'url',
        'icon',
        'section',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function linkPage()
    {
        return $this->belongsTo(LinkPage::class);
    }
}

