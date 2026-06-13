<?php

namespace App\Models;

use DateTimeInterface;
use App\Models\MarketplaceFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Marketplace extends Model
{
    use HasFactory;

    public $table = 'marketplaces';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function kontak()
    {
        return $this->belongsTo(Kontak::class, 'kontak_id');
    }

    public function kas()
    {
        return $this->belongsTo(AkunDetail::class, 'kas_id');
    }

    public function kasPenarikan()
    {
        return $this->belongsTo(AkunDetail::class, 'penarikan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'iklan');
    }

    public function getExpiredAttribute()
    {
        if ($this->autosinkron) {
            return $this->autosinkron_expired;
        }
    }

    public function getSinkronAttribute()
    {

        if ($this->marketplace == 'shopee') {
            if ($this->autosinkron) {
                return 'putuskan';
            } else {

                $format = MarketplaceFormat::where('marketplace', 'shopee')->where('jenis', 'order')->first();

                // Path HARUS dimulai dengan "/" untuk signature Shopee
                $path = "/api/v2/shop/auth_partner";
                // id sebagai PATH param (bukan query) supaya redirect URL tidak punya
                // query string. Shopee akan menambahkan ?code=...&shop_id=... sendiri
                // saat callback, sehingga aman dari penggabungan query yang malformed.
                $redirectUrl = url('shopee/auth/' . $this->id);
                $timest = time();
                $baseString = sprintf("%s%s%s", $format->partnerId, $path, $timest);
                $sign = hash_hmac('sha256', $baseString, $format->partnerKey);

                // Hapus trailing slash dari host untuk menghindari double slash
                $host = rtrim($format->host, '/');
                // redirect tetap di-urlencode agar aman sebagai nilai query param Shopee
                $shopeeConnectUrl = sprintf("%s%s?partner_id=%s&timestamp=%s&sign=%s&redirect=%s", $host, $path, $format->partnerId, $timest, $sign, urlencode($redirectUrl));
                return "<a href=\"" . $shopeeConnectUrl . "\">sinkronkan</a>";
            }
        } else {
            return 'blm bisa';
        }
    }
}
