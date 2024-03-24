<?php

namespace Modules\Core\Services\Shopify;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    private GuzzleClient $client;

    public function __construct(string $shop, string $accessToken)
    {
        $this->client = new GuzzleClient([
            "base_uri" => "https://$shop/admin/api/2024-01/",
            "headers" => [
                "Content-Type" => "application/json",
                "X-Shopify-Access-Token" => $accessToken,
            ],
        ]);
    }

    public function getClient(): GuzzleClient
    {
        return $this->client;
    }
}
