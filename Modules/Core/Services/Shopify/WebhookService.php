<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class WebhookService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function create(array $webhookData): stdClass
    {
        try {
            $response = $this->client->getClient()->post("webhooks.json", [
                "json" => $webhookData,
            ]);
            $content = json_decode($response->getBody()->getContents());
            return $content->webhook;
        } catch (GuzzleException $e) {
            throw new Exception("Error creating webhook: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function findAll(): array
    {
        try {
            $response = $this->client->getClient()->get("webhooks.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->webhooks;
        } catch (GuzzleException $e) {
            throw new Exception("Error listing webhooks: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function find(int $webhookId): stdClass
    {
        try {
            $response = $this->client->getClient()->get("webhooks/$webhookId.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->webhook;
        } catch (GuzzleException $e) {
            throw new Exception("Error retrieving webhook: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function delete(int $webhookId): stdClass
    {
        try {
            $response = $this->client->getClient()->delete("webhooks/$webhookId.json");
            return json_decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new Exception("Error deleting webhook: " . $e->getMessage());
        }
    }
}
