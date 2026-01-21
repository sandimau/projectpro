<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\MarketplaceFormat;

trait ShopeeApi
{
    protected function ambilApi($marketplace, $path, $param = [])
    {
        try {
            $accessToken = $this->ambilToken($marketplace);

            if ($accessToken) {

                $path = "/api/v2/" . $path;

                $format = MarketplaceFormat::shopee();

                $curl = curl_init();

                $timest = time();

                $baseString = sprintf("%s%s%s%s%s", $format->partnerId, $path, $timest, $accessToken, $marketplace->shop_id);
                $param['sign'] = hash_hmac('sha256', $baseString, $format->partnerKey);
                $param['partner_id'] = $format->partnerId;
                $param['timestamp'] = $timest;
                $param['access_token'] = $this->ambilToken($marketplace);
                $param['shop_id'] = $marketplace->shop_id;


                $url = $format->host . $path . "?" . http_build_query($param);


                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));
                $response = curl_exec($curl);

                if (curl_errno($curl)) {
                    $curlError = curl_error($curl);
                    curl_close($curl);
                    Log::error('ambilApi - cURL Error: ' . $curlError, [
                        'path' => $path,
                        'shop_id' => $marketplace->shop_id
                    ]);
                    return ['error' => 'cURL Error: ' . $curlError];
                }

                curl_close($curl);

                return json_decode($response, true);
            } else {
                Log::error('ambilApi - Token gagal diambil', [
                    'marketplace_id' => $marketplace->id ?? null,
                    'shop_id' => $marketplace->shop_id ?? null,
                    'path' => $path
                ]);
                return ['error' => 'token gagal diambil'];
            }
        } catch (\Exception $e) {
            Log::error('ambilApi - Exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => 'Exception: ' . $e->getMessage()];
        }
    }

    protected function ambilTokenPertama($code, $shopId)
    {

        $format = MarketplaceFormat::shopee();

        $path = "auth/token/get";
        $body = array("code" => $code,  "shop_id" => (int)$shopId, "partner_id" => $format->partnerId);

        $ret = $this->curlPost($path, $body);

        if ($ret)
            return [
                'access_token' => $ret['access_token'],
                'refresh_token' => $ret['refresh_token'],
                'access_expired' => ($ret['expire_in'] + time()),
            ];
        else return false;
    }

    public function ambilToken($marketplace)
    {
        try {
            if ($marketplace->access_expired >= (time() + 10))
                return $marketplace->access_token;

            $retry = 1;

            while ($retry < 5) {
                $marketplace = DB::table('marketplaces')->find($marketplace->id);
                if ($marketplace->access_expired < (time() + 10)) {
                    if ($marketplace->lock != 1) {

                        DB::table('marketplaces')->where('id', $marketplace->id)->update(['lock' => 1]);

                        $path = "auth/access_token/get";
                        $format = MarketplaceFormat::shopee();
                        $body = array("partner_id" => $format->partnerId, "shop_id" => (int)$marketplace->shop_id, "refresh_token" => $marketplace->refresh_token);

                        $ret = $this->curlPost($path, $body);

                        if ($ret) {
                            // Save new access token to database

                            DB::table('marketplaces')->where('id', $marketplace->id)->update([
                                'access_token' => $ret['access_token'],
                                'refresh_token' => $ret['refresh_token'],
                                'access_expired' => ($ret['expire_in'] + time()),
                                'lock' => 0
                            ]);

                            return $ret['access_token'];
                        } else {
                            DB::table('marketplaces')->where('id', $marketplace->id)->update(['lock' => 0]);
                            Log::error('ambilToken - Gagal refresh token', [
                                'marketplace_id' => $marketplace->id,
                                'shop_id' => $marketplace->shop_id,
                                'path' => $path,
                                'retry' => $retry
                            ]);
                            return false;
                        }
                    }
                } else
                    return $marketplace->access_token;

                $retry++;
                usleep(1000000);
            }

            Log::error('ambilToken - Retry maksimal tercapai', [
                'marketplace_id' => $marketplace->id ?? null,
                'shop_id' => $marketplace->shop_id ?? null,
                'max_retry' => 5
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('ambilToken - Exception: ' . $e->getMessage(), [
                'marketplace_id' => $marketplace->id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function curlPost($path, $body)
    {

        $path = "/api/v2/" . $path;
        $format = MarketplaceFormat::shopee();

        $timest = time();
        $baseString = sprintf("%s%s%s", $format->partnerId, $path, $timest);
        $sign = hash_hmac('sha256', $baseString, $format->partnerKey);
        $url = sprintf("%s%s?partner_id=%s&timestamp=%s&sign=%s", $format->host, $path, $format->partnerId, $timest, $sign);


        $c = curl_init($url);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($c);

        $ret = json_decode($result, true);


        if (!empty($ret["error"])) {
            Log::error('curlPost path:' . $path . ' body:' . json_encode($body), $ret);
            return false;
        } else
            return $ret;
    }
}
