<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Abstracts\GatewayServicesAbstract;
use Modules\Core\Entities\Gateway;

class MalgaService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [Gateway::MALGA_PRODUCTION_ID, Gateway::MALGA_SANDBOX_ID];

        $this->gatewayName = "Vega";

        $this->companyColumnBalance = "vega_balance";
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::MALGA_PRODUCTION_ID : Gateway::MALGA_SANDBOX_ID;
    }
}
