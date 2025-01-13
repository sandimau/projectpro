<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'orders';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        Order::saving(function ($model) {

            $total = 0;

            $batal = Produksi::where('nama', 'batal')->first()->id;

            foreach ($model->orderDetail as $detail) {

                if ($detail->produksi_id != $batal) {
                    $total += $detail->jumlah * $detail->harga;
                }
            }
            $model->total = $total - $model->diskon + $model->ongkir;

        });
    }

    public function getKekuranganAttribute()
    {
        return $this->total - $this->bayar;
    }

    public function kontak()
    {
        return $this->belongsTo(Kontak::class, 'kontak_id');
    }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function scopeBelumLunas($query)
    {
        $query->whereRaw('total > bayar');
        $query->orderBy('id','desc');
        return $query;
    }

    public function getListprodukAttribute()
    {
        $yy = array();
        foreach ($this->orderDetail as $item) {
            $yy[] = $item->produk->nama;
        }
        return implode(', ', $yy);
    }

    public function scopeOmzetTahun($query)
    {
        $query->select(DB::raw('YEAR(created_at) as year'), DB::raw('SUM(total) as sum'));
        $query->whereRaw('total');
        $query->groupBy('year');
        return $query;
    }

    public function scopeOmzetBulan($query,$var)
    {
        $query->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('EXTRACT(YEAR_MONTH FROM created_at) as month'),
            DB::raw('MONTHNAME(created_at) as monthname'),
            DB::raw('SUM(total) as omzet')
        );
        // $query->where( DB::raw('YEAR(created_at)'), '=', $var );
        $query->whereRaw('total');
        $query->groupBy('month');
        $query->orderBy('created_at');
        return $query;
    }
}
