<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Belanja extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'belanjas';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    public function akun_detail()
    {
        return $this->belongsTo(AkunDetail::class, 'akun_detail_id');
    }

    public function kontak()
    {
        return $this->belongsTo(Kontak::class);
    }

    public function belanjaDetail()
    {
        return $this->hasMany(BelanjaDetail::class);
    }

    public function getProdukAttribute()
    {
        $yy = array();

        foreach ($this->belanjaDetail as $item) {
            if ($item->produk) {
                $yy[$item->produk_id] = $item->produk->nama;
            }
        }
        if (empty($yy)) {
            return 'belum diset';
        } else {
            return implode(', ', $yy);
        }
    }
}
