<?php

declare(strict_types=1);

namespace Modules\GatewayIntegrations\Clients;

use GuzzleHttp\Client;

class ShortIOClient
{
    public function __construct(
        public readonly Client $client,
    ) {
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
