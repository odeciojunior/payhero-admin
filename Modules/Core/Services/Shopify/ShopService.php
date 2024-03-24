<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class ShopService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function show(): stdClass
    {
        try {
            $response = $this->client->getClient()->get("shop.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->shop;
        } catch (GuzzleException $e) {
            throw new Exception("Error retrieving shop properties: " . $e->getMessage());
        }
    }
}
