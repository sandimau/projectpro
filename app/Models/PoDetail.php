<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoDetail extends Model
{
    protected $guarded = [];
    protected $table = "produk_po_detail";

    public function po()
    {
        return $this->belongsTo(Po::class);
    }

    public function scopeProses($query)
    {
        return $query->
            join('produk_po', 'produk_po.id', '=', 'produk_po_detail.po_id')
            ->orderBy('tglKedatangan', 'asc')
            ->where('produk_po.status', 'proses');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
