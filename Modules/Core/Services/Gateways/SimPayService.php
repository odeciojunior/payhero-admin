<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Entities\Gateway;
use Modules\Core\Abstracts\GatewayServicesAbstract;

class SimPayService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [Gateway::SIMPAY_PRODUCTION_ID, Gateway::SIMPAY_SANDBOX_ID];

        $this->gatewayName = "Vega";

        $this->companyColumnBalance = "vega_balance";
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::SIMPAY_PRODUCTION_ID : Gateway::SIMPAY_SANDBOX_ID;
    }
}
