<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class InventoryService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function find(int $inventoryItemId): stdClass
    {
        try {
            $response = $this->client->getClient()->get("inventory_items/$inventoryItemId.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->inventory_item;
        } catch (GuzzleException $e) {
            throw new Exception("Error retrieving inventory item: " . $e->getMessage());
        }
    }
}
