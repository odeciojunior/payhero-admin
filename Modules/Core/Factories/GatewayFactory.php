<?php

declare(strict_types=1);

namespace Modules\Core\Factories;

use App\Shared\infrastructure\Gateways\MonetixGateway;
use Modules\Core\Entities\Gateway;
use Modules\Core\Exceptions\GatewayNotFound;
use Modules\Core\Interfaces\GatewayInterface;

class GatewayFactory
{
    /**
     * @throws GatewayNotFound
     */
    public static function make(string $gateway): GatewayInterface
    {
        return match ($gateway) {
            'monetix' => new MonetixGateway(
                app()->isProduction() ? Gateway::MONETIX_PRODUCTION_ID : Gateway::MONETIX_SANDBOX_ID,
            ),
            default => throw new GatewayNotFound(sprintf('Gateway %s not found', $gateway)),
        };
    }

    /**
     * @throws GatewayNotFound
     */
    public static function getGatewayId(string $gateway): int
    {
        return match ($gateway) {
            'monetix' => app()->isProduction() ? Gateway::MONETIX_PRODUCTION_ID : Gateway::MONETIX_SANDBOX_ID,
            default => throw new GatewayNotFound(sprintf('Gateway %s not found', $gateway)),
        };
    }
}
