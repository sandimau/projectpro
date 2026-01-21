<?php

namespace App\Http\Controllers\Webhook;

use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ShopeeApi;

class ShopeeLivePushController extends Controller
{
    use ShopeeApi;
    /**
     * Handle Shopee Live Push callback
     *
     * Endpoint ini menerima notifikasi dari Shopee Live Push
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        // Log semua request untuk debugging
        Log::channel('shopee')->info('Shopee Live Push Received', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);

        try {
            // Ambil data dari request
            $data = $request->all();

            // Verifikasi signature jika diperlukan (opsional)
            // $this->verifySignature($request);

            // Proses berdasarkan tipe event
            $eventType = $data['type'] ?? $data['event_type'] ?? null;

            switch ($eventType) {
                case 'live_start':
                    $this->handleLiveStart($data);
                    break;

                case 'live_end':
                    $this->handleLiveEnd($data);
                    break;

                case 'live_order':
                    $this->handleLiveOrder($data);
                    break;

                case 'live_product':
                    $this->handleLiveProduct($data);
                    break;

                default:
                    Log::channel('shopee')->info('Unknown event type', ['type' => $eventType, 'data' => $data]);
                    break;
            }

            // Shopee mengharapkan response 200 OK
            return response()->json([
                'code' => 0,
                'message' => 'success'
            ], 200);
        } catch (\Exception $e) {
            Log::channel('shopee')->error('Shopee Live Push Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Tetap return 200 agar Shopee tidak retry terus-menerus
            return response()->json([
                'code' => 0,
                'message' => 'received'
            ], 200);
        }
    }

    /**
     * Handle live streaming dimulai
     */
    protected function handleLiveStart(array $data)
    {
        Log::channel('shopee')->info('Live Started', $data);

        // Tambahkan logic Anda di sini
        // Contoh: kirim notifikasi, update status, dll
    }

    /**
     * Handle live streaming berakhir
     */
    protected function handleLiveEnd(array $data)
    {
        Log::channel('shopee')->info('Live Ended', $data);

        // Tambahkan logic Anda di sini
        // Contoh: update statistik, simpan ringkasan, dll
    }

    /**
     * Handle order dari live streaming
     */
    protected function handleLiveOrder(array $data)
    {
        Log::channel('shopee')->info('Live Order Received', $data);

        // Tambahkan logic Anda di sini
        // Contoh: buat order baru, update stok, dll
    }

    /**
     * Handle update produk dari live streaming
     */
    protected function handleLiveProduct(array $data)
    {
        Log::channel('shopee')->info('Live Product Update', $data);

        // Tambahkan logic Anda di sini
    }

    /**
     * Verifikasi signature dari Shopee (opsional)
     * Gunakan Live Push Partner Key untuk verifikasi
     */
    protected function verifySignature(Request $request): bool
    {
        $partnerKey = config('services.shopee.live_push_partner_key');

        if (empty($partnerKey)) {
            return true; // Skip verification jika key tidak diset
        }

        $signature = $request->header('X-Shopee-Signature') ?? $request->header('signature');
        $timestamp = $request->header('X-Shopee-Timestamp') ?? $request->header('timestamp');
        $body = $request->getContent();

        // Buat expected signature
        $expectedSignature = hash_hmac('sha256', $timestamp . $body, $partnerKey);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::channel('shopee')->warning('Invalid signature', [
                'expected' => $expectedSignature,
                'received' => $signature
            ]);
            throw new \Exception('Invalid signature');
        }

        return true;
    }

    public function auth()
    {
        $id = request()->route('id') ?? request()->get('id');
        $code = request()->input('code'); // Authorization code received from Shopee
        $shopId = (int)request()->input('shop_id');

        $config = Marketplace::find($id);

        // Exchange authorization code for access token and refresh token
        $tokens = $this->ambilTokenPertama($code, $shopId);

        if (!$tokens) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan token dari Shopee'
            ], 400);
        }

        $accessToken = $tokens['access_token'];
        $refreshToken = $tokens['refresh_token'];
        $accessExpired = $tokens['access_expired'];

        $config->update(
            [
                'shop_id' => $shopId,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'access_expired' => $accessExpired,
            ]
        );

        $toko = $this->ambilApi($config->fresh(), "shop/get_shop_info");

        if (isset($toko['expire_time'])) {
            $expired = date('Y-m-d H:i:s', $toko['expire_time']);

            $config->update(
                [
                    'autosinkron' => $toko['shop_name'],
                    'autosinkron_expired' => $expired,
                ]
            );
        }

        return redirect()->route('marketplaces.index')->with('success', 'Berhasil terhubung dengan Shopee: ' . ($toko['shop_name'] ?? 'Unknown'));
    }

    public function push()
    {
        $hasil = request()->all();

        $nota = $hasil['data']['ordersn'];
        $input = [[
            'shop_id' => $hasil['shop_id'],
            'nota' => $nota,
            'mp' => 'shopee',
            'created_at' => now(),
            'updated_at' => now(),
            'status' => $hasil['data']['status'],
        ]];

        DB::table('marketplace_buffers')->upsert($input, ['nota'], ['updated_at', 'status']);

        // Shopee mengharapkan response 200 dengan code 0
        return response()->json([
            'code' => 0,
            'message' => 'success'
        ], 200);
    }

    /**
     * Manual refresh token menggunakan refresh_token dari database
     *
     * URL: /shopee/manualRefresh?id={marketplace_id}
     */
    public function manualRefreshToken()
    {
        $id = request()->get('id');

        // Validasi input
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter id tidak ada',
                'example' => url('/shopee/manualRefresh') . '?id=1'
            ], 400);
        }

        $config = Marketplace::find($id);

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Marketplace dengan id ' . $id . ' tidak ditemukan'
            ], 404);
        }

        // Ambil data dari database
        $refreshToken = $config->refresh_token;
        $shopId = (int) $config->shop_id;

        if (!$refreshToken || !$shopId) {
            return response()->json([
                'success' => false,
                'message' => 'Data refresh_token atau shop_id tidak ada di database. Silakan otorisasi ulang.',
                'data' => [
                    'refresh_token' => $refreshToken ? 'ada' : 'tidak ada',
                    'shop_id' => $shopId ?: 'tidak ada',
                ]
            ], 400);
        }

        try {
            // Panggil API refresh token
            $path = "auth/access_token/get";
            $format = \App\Models\MarketplaceFormat::shopee();
            $body = [
                "partner_id" => $format->partnerId,
                "shop_id" => $shopId,
                "refresh_token" => $refreshToken
            ];

            $ret = $this->curlPost($path, $body);

            if ($ret && !empty($ret['access_token'])) {
                // Simpan token baru ke database
                $config->update([
                    'access_token' => $ret['access_token'],
                    'refresh_token' => $ret['refresh_token'],
                    'access_expired' => ($ret['expire_in'] + time()),
                ]);

                // Ambil info toko
                $toko = $this->ambilApi($config->fresh(), "shop/get_shop_info");

                if (isset($toko['shop_name'])) {
                    $expired = date('Y-m-d H:i:s', $toko['expire_time'] ?? time() + 86400);
                    $config->update([
                        'autosinkron' => $toko['shop_name'],
                        'autosinkron_expired' => $expired,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Token berhasil di-refresh dan disimpan!',
                    'data' => [
                        'marketplace_id' => $id,
                        'shop_id' => $shopId,
                        'shop_name' => $toko['shop_name'] ?? null,
                        'access_token' => substr($ret['access_token'], 0, 10) . '...',
                        'expire_in' => $ret['expire_in'] . ' detik',
                        'expire_at' => date('Y-m-d H:i:s', $ret['expire_in'] + time()),
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal refresh token. Refresh token mungkin sudah expired (>30 hari). Silakan otorisasi ulang.',
                    'response' => $ret
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Manual Refresh Token Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
