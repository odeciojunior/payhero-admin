<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Abstracts\GatewayServicesAbstract;
use Modules\Core\Entities\Gateway;

class PayupService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [Gateway::PAYUP_PRODUCTION_ID, Gateway::PAYUP_SANDBOX_ID];

        $this->gatewayName = "Vega";

        $this->companyColumnBalance = "vega_balance";
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::PAYUP_PRODUCTION_ID : Gateway::PAYUP_SANDBOX_ID;
    }
}
