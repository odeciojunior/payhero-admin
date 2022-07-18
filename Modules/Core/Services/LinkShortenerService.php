<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Log;

class LinkShortenerService
{
    const BASE_URL = 'https://api.short.io';

    public function shorten($url, $path = null)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $data = [
            'domain' => 'mud.ae',
            'originalUrl' => $url,
            'allowDuplicates' => false,
        ];
        if (isset($path)) {
            $data['path'] = $path;
        }

        $result = $this->doRequest('/links', 'POST', $data);

        if (!$result) {
            Log::warning('Link URL invalido (LinkShortenerService - shorten) - ' . $url);
        }

        return $result;
    }

    private function doRequest(string $uri = "/", string $method = "GET", array $data = null, array $headers = [])
    {
        $curl = curl_init();

        $url = self::BASE_URL . $uri;
        $method = strtoupper($method);
        $headers = array_merge($headers, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: ' . env('SHORTIO_API_KEY'),
        ]);

        if (!empty($data)) {
            if ($method === "GET") {
                $url = sprintf("%s?%s", $url, http_build_query($data));
            } else {
                $data = json_encode($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        }

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if (isset($info['http_code']) && $info['http_code'] != 200) {
            return false;
        }

        return json_decode($result);
    }
}
