<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class ProductService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function findAll(array $queryParams = []): PaginatedResource
    {
        try {
            $response = $this->client->getClient()->get("products.json", [
                "query" => $queryParams,
            ]);

            return new PaginatedResource($response, "products");
        } catch (GuzzleException $e) {
            throw new Exception("Error listing products: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function find(int $productId): stdClass
    {
        try {
            $response = $this->client->getClient()->get("products/$productId.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->product;
        } catch (GuzzleException $e) {
            throw new Exception("Error retrieving product: " . $e->getMessage());
        }
    }
}
