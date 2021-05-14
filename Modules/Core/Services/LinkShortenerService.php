<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Log;

class LinkShortenerService
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = "https://mud.ae/api/?key=" . getenv('MUD_AE_API_KEY');
    }

    function shorten($url)
    {
        $url = trim($url);

        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        } else {
            $url = urlencode($url);

            $apiCall = $this->apiUrl . "&url={$url}&format=text";

            $linkValidate = $this->http($apiCall);

            if ($linkValidate == '') {
                Log::warning('Link URL invalido (LinkShortenerService - shorten) - ' . $url);

                return $linkValidate;
            } else {
                return $linkValidate;
            }
        }
    }

    public function http(string $url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($curl);
        curl_close($curl);

        return $resp;
    }
}
