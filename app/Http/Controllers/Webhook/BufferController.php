<?php

namespace App\Http\Controllers\Webhook;

use App\Models\Produk;
use App\Models\Belanja;
use App\Models\ProjectMp;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use App\Models\ProdukStok;
use App\Models\Marketplace;
use App\Models\ProdukModel;
use App\Models\BelanjaDetail;
use App\Models\MarketplaceLog;
use App\Models\ProjectMpDetail;
use App\Models\MarketplaceBuffer;
use App\Models\ProdukMarketplace;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ShopeeApi;

class BufferController extends Controller
{
    use ShopeeApi;

    public function wallet($marketplace = false)
    {
        if ($marketplace) {
            $marketplaces = [$marketplace];
        } else {
            $marketplaces = Marketplace::where('marketplace', 'shopee')->whereNotNull('shop_id')->get();
        }

        foreach ($marketplaces as $marketplace) {
            $loop_api = true;
            $page_no = 0;

            $projectMp = [];
            $penarikanMp = [];
            $iklan = [];
            $ketemu = false;
            $baru = false;
            $totalIklan = 0;

            /////ambil data terakhir
            $terakhir = BukuBesar::where('akun_detail_id', $marketplace->kas_id)->latest()->first();

            if (!$terakhir) {
                $baru = true;
                $from = strtotime("-3 days");
            } else {
                $from = strtotime($terakhir->created_at) - 1;
            }

            while ($loop_api) {

                $page_no++;
                $param = [
                    'page_no' => $page_no,
                    'page_size' => '100',
                    'create_time_from' => $from,
                    'create_time_to' => strtotime("now")
                ];

                $api = $this->ambilApi($marketplace, 'payment/get_wallet_transaction_list', $param);
                if (!empty($api['response'])) {
                    $transcation = $api['response']['transaction_list'];

                    if (!$api['response']['more']) {
                        $loop_api = false;
                    }

                    //update saldo kas marketplace
                    if ($page_no == 1) {
                        if (!empty($transcation[0])) {
                            $last = $transcation[0];
                        } else {
                            $last = false;
                            $ketemu = true;
                        }
                    }

                    //update project mp & buku besar penarikan
                    foreach ($transcation as $key => $value) {

                        if (!$baru) {
                            if (date("Y-m-d H:i:s", $value['create_time']) == $terakhir->created_at && $value['current_balance'] == $terakhir->debet) {
                                $ketemu = true;
                                break;
                            }
                        } else {
                            $ketemu = true;
                        }

                        //update iklan
                        if ($value['transaction_tab_type'] === 'wallet_wallet_payment') {
                            $iklan[] = [
                                'produk_id' => $marketplace->iklan_id,
                                'harga' => abs($value['amount']),
                                'jumlah' => 1,
                                'keterangan' => $value['transaction_tab_type'] . ' ' . $marketplace->nama,
                                'jenis' => 'iklan',
                                'detail_id' => $marketplace->id
                            ];
                            $totalIklan += abs($value['amount']);
                        }

                        //update selisih dengan project mp
                        if ($value['transaction_tab_type'] == "wallet_order_income") {
                            $project = ProjectMp::where('nota', $value['order_sn'])->first();

                            $persen = 0;
                            if ($project && $project->total > 0) {
                                $persen = ($project->total - $value['amount']) / $project->total * 100;
                            }

                            $projectMp[] = [
                                'nota' => $value['order_sn'],
                                'bersih' => $value['amount'],
                                'persen' => floor($persen)
                            ];
                        }

                        //update penarikan
                        if ($value['transaction_tab_type'] == 'wallet_withdrawals' and $value['amount'] != 0) {

                            $nilai = abs($value['amount']);

                            //masukan kedalam array
                            $penarikanMp[] = [
                                'akun_detail_id' => $marketplace->penarikan_id,
                                'kode' => 'trf',
                                'ket' => $value['transaction_tab_type'] . ' ' . $marketplace->nama,
                                'detail_id' => $value['withdrawal_id'],
                                'debet' => $nilai,
                                'kredit' => 0,
                                'created_at' => date("Y-m-d H:i:s", $value['create_time']),
                                'updated_at' => now(),
                            ];
                        }
                    }
                } else {
                    $loop_api = false;
                }
            }

            if ($ketemu) {

                if ($last) {
                    BukuBesar::where('akun_detail_id', $marketplace->kas_id)->delete();
                    BukuBesar::create([
                        'akun_detail_id' => $marketplace->kas_id,
                        'kode' => 'byr',
                        'ket' => 'saldo akhir',
                        'debet' => $last['current_balance'],
                        'kredit' => 0,
                        'created_at' => date("Y-m-d H:i:s", $last['create_time'])
                    ]);
                }

                //update project mp berdasarkan nota
                if (!empty($projectMp)) {
                    ProjectMp::upsert($projectMp, ['nota'], ['bersih', 'persen']);
                }

                //insert buku besar penarikan
                if (!empty($penarikanMp)) {
                    BukuBesar::insert($penarikanMp);

                    $AkunDetail = AkunDetail::find($marketplace->penarikan_id);
                    $AkunDetail->updateSaldo();
                }

                if (!empty($iklan)) {
                    $belanja = Belanja::create([
                        'nota' => request()->nota ? request()->nota : rand(1000000, 100),
                        'total' => $totalIklan,
                        'kontak_id' => $marketplace->kontak_id,
                        'created_at' => date("Y-m-d H:i:s")
                    ]);

                    foreach ($iklan as $item) {
                        $item['belanja_id'] = $belanja->id;
                        BelanjaDetail::create($item);
                    }
                }
            } else {
                MarketplaceLog::create([
                    'isi' => 'gagal update saldo wallet',
                    'jenis' => 'update saldo',
                    'shop_id' => $marketplace->shop_id,
                    'marketplace' => $marketplace->nama,
                    'tanggal' => now()
                ]);
            }
        }
    }

    public function prosesBuffer()
    {
        $ambil = MarketplaceBuffer::where('mp', 'shopee')
            ->whereNull('project_id')
            ->orderBy('shop_id')
            ->get();

        $mps = $ambil->groupBy('shop_id');

        foreach ($mps as $shop_id => $mp) {

            $marketplace = Marketplace::where('shop_id', $shop_id)->first();

            if ($marketplace) {

                $nota = [];
                $i = 0;
                foreach ($mp as $push) {
                    $i++;
                    if ($i > 40) {
                        break;
                    }
                    if ($push->status != 'CANCELLED')
                        $nota[] = $push->nota;
                    else
                        MarketplaceBuffer::where('mp', 'shopee')->where('id', $push->id)->delete();
                }

                $param = [
                    'order_sn_list' => implode(',', $nota),
                    "response_optional_fields" => "item_list,buyer_username,total_amount,shipping_carrier"
                ];

                $api = $this->ambilApi($marketplace, 'order/get_order_detail', $param);

                if (!empty($api['response'])) {

                    foreach ($api['response']['order_list'] as $orderlist) {

                        $keterangan = preg_replace('/[^\x20-\x7E]/', '', $orderlist['message_to_seller']);

                        $nota = $orderlist['order_sn'];

                        $baru = true;
                        try {
                            $projectMp = ProjectMp::create([
                                'marketplace_id' => $marketplace->id,
                                'cabang_id' => $marketplace->cabang_id,
                                'nota' => $nota,
                                'total' => $orderlist['total_amount'],
                                'konsumen' => $orderlist['buyer_username'],
                                'keterangan' => $keterangan,
                                'shipping' => $orderlist['shipping_carrier'],
                            ]);
                            $project_id = $projectMp->id;
                        } catch (\Exception $e) {
                            $project_id = ProjectMp::where('nota', $nota)->first()->id;
                            $baru = false;

                            MarketplaceBuffer::where('mp', 'shopee')->where('nota', $nota)->update([
                                'project_id' => $project_id,
                                'marketplace_id' => $marketplace->id
                            ]);
                        }

                        if ($baru) {
                            $items = $orderlist['item_list'];

                            $orderdetail = [];

                            $custom = null;
                            $hargaTotal = 0;
                            foreach ($items as $item) {

                                $sku = $item['model_sku'];
                                if (empty($sku))
                                    $sku = $item['item_sku'];

                                if ($sku != 'NON_PRODUK') {
                                    if (strpos($sku, 'CUSTOM_') !== false) {
                                        $custom = 1;
                                        $sku = str_replace('CUSTOM_', "", $sku);
                                    }

                                    $jumlah = $item['model_quantity_purchased'];
                                    $harga = $item['model_discounted_price'];

                                    $hargaTotal += $harga * $jumlah;

                                    $paket = 1;
                                    if (strpos($sku, '_') !== false) {
                                        $skuParts = explode('_', $sku);
                                        $sku = $skuParts[0];
                                        $paket = $skuParts[1];
                                        $jumlah = $jumlah * $paket;
                                        $harga = floor($harga / $paket);
                                    }

                                    $item_id = $item['item_id'];
                                    $model_id = $item['model_id'];

                                    ProdukMarketplace::upsert([
                                        [
                                            'model_id' => $model_id,
                                            'item_id' => $item_id,
                                            'produk_id' => $sku,
                                            'marketplace_id' => $marketplace->id,
                                            'paket' => $paket,
                                            'harga' => $harga,
                                            'nama' => $item['item_name'],
                                            'varian' => $item['model_name'],
                                            'created_at' => now(),
                                            'updated_at' => now()
                                        ]
                                    ], ['model_id', 'item_id'], ['produk_id', 'paket', 'harga', 'nama', 'varian', 'updated_at']);

                                    $hpp = 0;
                                    $produk = Produk::find($sku);
                                    $model_id = $produk->produk_model_id ?? 0;
                                    $stok = ProdukModel::find($model_id)->stok ?? 0;
                                    if ($stok == 1) {
                                        $this->mpBeli($sku, $marketplace, $jumlah, $project_id);
                                        $hpp = $produk->hpp ?? 0;
                                    }
                                    $orderdetail[] = [
                                        'harga' => $harga,
                                        'jumlah' => $jumlah,
                                        'produk_id' => $sku,
                                        'nota' => $nota,
                                        'tema' => $item['model_name'],
                                        'project_id' => $project_id,
                                        'hpp' => $hpp,
                                    ];
                                }
                            }

                            if (!empty($orderdetail)) {
                                ProjectMpDetail::insert($orderdetail);
                                ProjectMp::where('id', $project_id)->update(['total' => $hargaTotal]);
                            }

                            MarketplaceBuffer::where('mp', 'shopee')->where('nota', $nota)->update([
                                'project_id' => $project_id,
                                'custom' => $custom,
                                'marketplace_id' => $marketplace->id
                            ]);
                        }
                    }
                }
            }
        }

        MarketplaceBuffer::where('mp', 'shopee')
            ->whereNotNull('project_id')
            ->where('status', 'COMPLETED')
            ->delete();

        /////////////3. memproses yg cancel
        $this->hapusCancelShopee();
    }

    public function hapusCancelShopee()
    {
        $ambil = MarketplaceBuffer::where('mp', 'shopee')
            ->whereNotNull('project_id')
            ->where('status', 'CANCELLED')
            ->get();

        foreach ($ambil as $cancel) {
            $project = ProjectMp::find($cancel->project_id);

            $details = ProjectMpDetail::select('project_mp_details.produk_id', 'produk_models.stok')
                ->where('project_id', $cancel->project_id)
                ->leftJoin('produks', 'produks.id', '=', 'project_mp_details.produk_id')
                ->leftJoin('produk_models', 'produk_models.id', '=', 'produks.produk_model_id')
                ->get();

            ProdukStok::where('project_id', $cancel->project_id)->where('kode', 'shp')->forceDelete();

            foreach ($details as $detail) {
                if ($detail->stok == 1)
                    $this->updateStokMp($detail->produk_id, $project->cabang_id, $project->company_id);
            }

            ProjectMpDetail::where('project_id', $cancel->project_id)->delete();
            ProjectMp::where('id', $cancel->project_id)->delete();
            MarketplaceBuffer::where('mp', 'shopee')->where('id', $cancel->id)->delete();
        }
    }

    public function bersihkanBuffer()
    {
        $buffers = MarketplaceBuffer::where('mp', 'shopee')
            ->where(function ($query) {
                $query->where('created_at', '<', now()->subDays(14))
                    ->orWhereNotIn('status', ['READY_TO_SHIP', 'CANCELLED', 'UNPAID', 'PROCESSED', 'SHIPPED', 'TO_CONFIRM_RECEIVE']);
            })
            ->where('status', '!=', 'TO_RETURN')
            ->orderBy('created_at', 'asc')
            ->limit(150)
            ->get();

        $buffers = $buffers->groupBy('shop_id');

        foreach ($buffers as $shop_id => $buffer) {
            $notaArray = [];
            foreach ($buffer as $detail) {
                $notaArray[] = $detail->nota;
            }
            $notaString = implode(',', $notaArray);
            $param = [
                'order_sn_list' => $notaString
            ];
            $marketplace = Marketplace::where('shop_id', $shop_id)->first();
            $api = $this->ambilApi($marketplace, 'order/get_order_detail', $param);

            if (!empty($api['response'])) {
                foreach ($api['response']['order_list'] as $orderlist) {
                    $nota = $orderlist['order_sn'];
                    $status = $orderlist['order_status'];
                    MarketplaceBuffer::where('mp', 'shopee')->where('nota', $nota)->update(['status' => $status]);
                }
            }
        }
    }

    public function updateBuffer()
    {
        $marketplaces = Marketplace::where('marketplace', 'shopee')
            ->whereNotNull('shop_id')
            ->get();

        foreach ($marketplaces as $marketplace) {

            $shop_id = $marketplace->shop_id;
            $buffers = MarketplaceBuffer::where('mp', 'shopee')
                ->where('shop_id', $shop_id)
                ->orderBy('id', 'asc')
                ->where('status', 'IN_CANCEL')
                ->limit(40)
                ->get();

            $notaArray = [];
            foreach ($buffers as $buffer) {
                $notaArray[] = $buffer->nota;

                //update status delivery failed
                $param = [
                    'order_sn' => $buffer->nota,
                ];

                $api = $this->ambilApi($marketplace, 'logistics/get_tracking_info', $param);
                if (!empty($api['response'])) {
                    if (isset($api['response']['order_sn']) && isset($api['response']['logistics_status'])) {
                        $nota = $api['response']['order_sn'];
                        $status = $api['response']['logistics_status'];
                        if ($status === 'LOGISTICS_DELIVERY_FAILED') {
                            MarketplaceBuffer::where('mp', 'shopee')
                                ->where('nota', $nota)
                                ->update(['status' => $status]);
                        }
                    }
                }
            }
            $notaString = implode(',', $notaArray);

            $param = [
                'order_sn_list' => $notaString
            ];

            $api = $this->ambilApi($marketplace, 'order/get_order_detail', $param);

            if (!empty($api['response'])) {
                foreach ($api['response']['order_list'] as $orderlist) {
                    $nota = $orderlist['order_sn'];
                    $status = $orderlist['order_status'];
                    MarketplaceBuffer::where('mp', 'shopee')->where('nota', $nota)->update(['status' => $status]);
                }
            }
        }
    }
}
