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

    public function buffer()
    {
        return $this->hasOne(MarketplaceBuffer::class, 'project_id');
    }

    public function getListprodukAttribute()
    {
        $yy = array();
        foreach ($this->details as $item) {
            $nama_produk = '';
            $nama_produk .= $item->produk->namaLengkap;
            $yy[] = $nama_produk;
        }
        return implode(', ', $yy);
    }
}
