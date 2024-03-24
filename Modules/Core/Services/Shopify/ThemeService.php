<?php

namespace Modules\Core\Services\Shopify;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class ThemeService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function findAll(): array
    {
        try {
            $response = $this->client->getClient()->get("themes.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->themes;
        } catch (GuzzleException $e) {
            throw new Exception("Error listing themes: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function find(int $themeId): stdClass
    {
        try {
            $response = $this->client->getClient()->get("themes/$themeId.json");
            $content = json_decode($response->getBody()->getContents());
            return $content->theme;
        } catch (GuzzleException $e) {
            throw new Exception("Error retrieving theme: " . $e->getMessage());
        }
    }
}
