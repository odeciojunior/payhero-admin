<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Entities\Gateway;
use Modules\Core\Abstracts\GatewayServicesAbstract;

class VolutiService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [Gateway::VOLUTI_PRODUCTION_ID, Gateway::VOLUTI_SANDBOX_ID];

        $this->gatewayName = "Vega";

        $this->companyColumnBalance = "vega_balance";
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::VOLUTI_PRODUCTION_ID : Gateway::VOLUTI_SANDBOX_ID;
    }
}
