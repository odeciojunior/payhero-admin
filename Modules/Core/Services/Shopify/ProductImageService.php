<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class ProductImageService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function find(int $productId, int $imageId): stdClass
    {
        try {
            $response = $this->client->getClient()->get("products/$productId/images/$imageId.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->image;
        } catch (GuzzleException $e) {
            throw new Exception("Error retrieving product image: " . $e->getMessage());
        }
    }
}
