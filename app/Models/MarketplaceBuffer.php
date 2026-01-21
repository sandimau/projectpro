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

    public function scopeDetail($query)
    {
        return $query
            ->select(
                'marketplace_buffers.*',
                'marketplace_buffers.project_id as order_id',
                'marketplace_buffers.status as statusMp',
                'marketplaces.nama as nama_marketplace',
                'marketplaces.warna as config_warna',
                'project_mps.created_at as tanggal',
                'project_mps.nota',
                'project_mps.konsumen',
                'project_mps.total',
                'project_mps.keterangan',
                'produk_models.nama as nama_model'
            )
            ->leftJoin('project_mps', 'project_mps.id', '=', 'marketplace_buffers.project_id')
            ->leftJoin('marketplaces', 'marketplace_buffers.marketplace_id', '=', 'marketplaces.id')
            ->leftJoin('project_mp_details', 'project_mps.id', '=', 'project_mp_details.project_id')
            ->leftJoin('produks', 'produks.id', '=', 'project_mp_details.produk_id')
            ->leftJoin('produk_models', 'produk_models.id', '=', 'produks.produk_model_id')
            ->orderBy('marketplace_buffers.status')
            ->orderBy('project_mps.created_at', 'asc')
            ->whereNotNull('marketplace_buffers.project_id');
    }

    public function scopeCustom($query)
    {
        return $query
            ->where('marketplace_buffers.custom', 1)
            ->where(function ($query2) {
                $query2->where('marketplace_buffers.status', 'PROCESSED')
                    ->orWhere('marketplace_buffers.status', 'READY_TO_SHIP')
                    ->orWhere('marketplace_buffers.status', 'UNPAID');
            });
    }

    public function scopePacking($query)
    {
        return $query
            ->where(function ($query) {
                $query->where('marketplace_buffers.status', 'PROCESSED')
                    ->orWhere('marketplace_buffers.status', 'READY_TO_SHIP');
            })
            ->whereNull('marketplace_buffers.custom');
    }
}
