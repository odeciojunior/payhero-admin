<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class AssetService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function findAll(int $themeId): array
    {
        try {
            $response = $this->client->getClient()->get("themes/$themeId/assets.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->assets;
        } catch (GuzzleException $e) {
            throw new Exception("Error listing assets: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function find(int $themeId, string $key): stdClass
    {
        try {
            $response = $this->client->getClient()->get("themes/$themeId/assets.json", [
                "query" => [
                    "asset[key]" => $key,
                ],
            ]);
            $content = json_decode($response->getBody()->getContents());
            return $content->asset;
        } catch (GuzzleException $e) {
            throw new Exception("Error retrieving asset: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function createOrUpdateAsset(int $themeId, string $key, string $value): stdClass
    {
        try {
            $response = $this->client->getClient()->put("themes/$themeId/assets.json", [
                "json" => [
                    "asset" => [
                        "key" => $key,
                        "value" => $value,
                    ],
                ],
            ]);
            $content = json_decode($response->getBody()->getContents());
            return $content->asset;
        } catch (GuzzleException $e) {
            throw new Exception("Error in asset operation: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function delete(int $themeId, string $key): stdClass
    {
        try {
            $response = $this->client->getClient()->delete("themes/$themeId/assets.json", [
                "query" => [
                    "asset[key]" => $key,
                ],
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new Exception("Error deleting asset: " . $e->getMessage());
        }
    }
}
