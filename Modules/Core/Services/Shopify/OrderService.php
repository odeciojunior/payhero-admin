<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class OrderService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function create(array $orderData): stdClass
    {
        try {
            $response = $this->client->getClient()->post("orders.json", [
                "json" => $orderData,
            ]);
            $content = json_decode($response->getBody()->getContents());
            return $content->order;
        } catch (GuzzleException $e) {
            throw new Exception("Error creating order: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function cancel(int $orderId, array $params = []): stdClass
    {
        try {
            $response = $this->client->getClient()->post("orders/$orderId/cancel.json", [
                "json" => $params,
            ]);
            $content = json_decode($response->getBody()->getContents());
            return $content->order;
        } catch (GuzzleException $e) {
            throw new Exception("Error canceling order: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function close(int $orderId): stdClass
    {
        try {
            $response = $this->client->getClient()->post("orders/$orderId/close.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->order;
        } catch (GuzzleException $e) {
            throw new Exception("Error closing order: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function find(int $orderId): stdClass
    {
        try {
            $response = $this->client->getClient()->get("orders/$orderId.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->order;
        } catch (GuzzleException $e) {
            throw new Exception("Error retrieving order: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function update(int $orderId, array $orderData): stdClass
    {
        try {
            $response = $this->client->getClient()->put("orders/$orderId.json", [
                "json" => $orderData,
            ]);
            $content = json_decode($response->getBody()->getContents());
            return $content->order;
        } catch (GuzzleException $e) {
            throw new Exception("Error updating order: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function delete(int $orderId): stdClass
    {
        try {
            $response = $this->client->getClient()->delete("orders/$orderId.json");
            return json_decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new Exception("Error deleting order: " . $e->getMessage());
        }
    }
}
