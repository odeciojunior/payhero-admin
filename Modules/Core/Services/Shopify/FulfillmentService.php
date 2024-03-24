<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class FulfillmentService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function findAll(int $orderId): array
    {
        try {
            $response = $this->client->getClient()->get("orders/$orderId/fulfillments.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->fulfillments;
        } catch (GuzzleException $e) {
            throw new Exception("Error retrieving fulfillments: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function cancel(int $fulfillmentId): stdClass
    {
        try {
            $response = $this->client->getClient()->post("fulfillments/$fulfillmentId/cancel.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->fulfillment;
        } catch (GuzzleException $e) {
            throw new Exception("Error canceling fulfillment: " . $e->getMessage());
        }
    }
}
