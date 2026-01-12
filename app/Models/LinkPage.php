<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkPage extends Model
{
    protected $table = 'link_pages';

    protected $fillable = [
        'slug',
        'title',
        'logo',
        'background_color',
        'text_color',
        'button_color',
        'button_text_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(LinkItem::class)->orderBy('order');
    }

    public function socialLinks()
    {
        return $this->hasMany(LinkItem::class)
            ->where('type', 'social')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function linkItems()
    {
        return $this->hasMany(LinkItem::class)
            ->where('type', 'link')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function getItemsBySection()
    {
        return $this->linkItems()
            ->get()
            ->groupBy('section');
    }
}

