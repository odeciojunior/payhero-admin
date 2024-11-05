<?php

declare(strict_types=1);

namespace Modules\GatewayIntegrations\Gateways\ShortenLinks\contract;

/**
 * "Interface" ShortenLinkGatewayInterface
 *
 * @package Modules\GatewayIntegrations\Gateways\ShortenLinks\contract
 */
interface ShortenLinkGatewayInterface
{
    /**
     * Deleting a short URL
     *
     * @param  string  $shortId  A URL completa que deve ser encurtada.
     * @return bool.
     */
    public function delete(string $shortId): bool;
}
