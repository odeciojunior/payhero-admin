<?php

declare(strict_types=1);

namespace Modules\GatewayIntegrations\Gateways\ShortenLinks;

use Illuminate\Support\Facades\Log;
use Modules\GatewayIntegrations\Clients\ShortIOClient;
use Modules\GatewayIntegrations\Gateways\ShortenLinks\contract\ShortenLinkGatewayInterface;
use Throwable;

/**
 * @see https://developers.short.io/docs Documentação Oficial
 */
class ShortIOGateway implements ShortenLinkGatewayInterface
{
    public function __construct(
        private readonly ShortIOClient $shortIOClient,
    ) {
    }

    public function delete(string $shortId): bool
    {
        try {
            $url = sprintf('/links/%s', $shortId);
            $response = $this->shortIOClient
                ->getClient()
                ->delete($url, [
                    'json' => []
                ]);
            $response = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            Log::info('short link deleted', $response);

            return $response['success'] ?? false;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }
}
