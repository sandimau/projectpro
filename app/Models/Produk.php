<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{

    public $table = 'produks';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = ['nama', 'hpp', 'status', 'produk_model_id'];

    public function getNamaLengkapAttribute()
    {
        if ($this->nama) {
            return $this->produkModel->kategori->nama . ' - ' . $this->produkModel->nama . ' (' . $this->nama . ')';
        } else {
            return $this->produkModel->kategori->nama . ' - ' . $this->produkModel->nama;
        }
    }

    public function akunDetail()
    {
        return $this->belongsTo(AkunDetail::class, 'akun_detail_id');
    }

    public function lastStok()
    {
        return $this->belongsToMany(Produk::class, 'produk_last_stoks', 'produk_id')->withPivot('saldo');
    }

    public function produkModel()
    {
        return $this->belongsTo(ProdukModel::class);
    }
}
