<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class ProductVariantService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function find(int $variantId): stdClass
    {
        try {
            $response = $this->client->getClient()->get("variants/$variantId.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->variant;
        } catch (GuzzleException $e) {
            throw new Exception("Error retrieving product variant: " . $e->getMessage());
        }
    }
}
