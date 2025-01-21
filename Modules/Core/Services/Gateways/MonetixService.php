<?php

declare(strict_types=1);

namespace Modules\Core\Services\Gateways;

use Modules\Core\Abstracts\GatewayServicesAbstract;
use Modules\Core\Entities\Gateway;

class MonetixService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [
            Gateway::MONETIX_PRODUCTION_ID,
            Gateway::MONETIX_SANDBOX_ID,
        ];
        $this->gatewayName = 'Vega';
        $this->companyColumnBalance = 'vega_balance';
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::MONETIX_PRODUCTION_ID : Gateway::MONETIX_SANDBOX_ID;
    }
}
