<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
    public function find(int $orderId): ?stdClass
    {
        try {
            $response = $this->client->getClient()->get("orders/$orderId.json");
            return json_decode(
                $response->getBody()->getContents(),
                false,
                512,
                JSON_THROW_ON_ERROR
            )->order;
        } catch (Throwable $t) {
            if ($t instanceof ClientException && $t->getCode() === Response::HTTP_FORBIDDEN) {
                Log::alert($t->getMessage());

                return null;
            }

            throw new Exception("Error retrieving order: ".$t->getMessage());
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
