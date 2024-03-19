<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Abstracts\GatewayServicesAbstract;
use Modules\Core\Entities\Gateway;

class VegaService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [
            Gateway::SAFE2PAY_PRODUCTION_ID,
            Gateway::SAFE2PAY_SANDBOX_ID,
            Gateway::IUGU_PRODUCTION_ID,
            Gateway::IUGU_SANDBOX_ID,
            Gateway::ABMEX_PRODUCTION_ID,
            Gateway::ABMEX_SANDBOX_ID,
            Gateway::SIMPAY_PRODUCTION_ID,
            Gateway::SIMPAY_SANDBOX_ID,
            Gateway::VEGA_PRODUCTION_ID,
            Gateway::VEGA_SANDBOX_ID,
        ];

        $this->gatewayName = "Vega";

        $this->companyColumnBalance = "vega_balance";
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::VEGA_PRODUCTION_ID : Gateway::VEGA_SANDBOX_ID;
    }
}
