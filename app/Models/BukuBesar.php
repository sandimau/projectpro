<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuBesar extends Model
{
    use HasFactory;

    public $table = 'buku_besars';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        BukuBesar::saving(function ($model) {

            $terakhir = $model->where('akun_detail_id', $model->akun_detail_id)->latest('id')->first()->saldo ?? 0 ;
            $model->saldo = $terakhir + $model->debet - $model->kredit;

            $akunDetail = AkunDetail::find($model->akun_detail_id);
            $akunDetail->update(['saldo' => $model->saldo]);

        });
    }
}
