<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Entities\Gateway;
use Modules\Core\Abstracts\GatewayServicesAbstract;

class ArmPayService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [Gateway::ARMPAY_PRODUCTION_ID, Gateway::ARMPAY_SANDBOX_ID];

        $this->gatewayName = "Vega";

        $this->companyColumnBalance = "vega_balance";
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::ARMPAY_PRODUCTION_ID : Gateway::ARMPAY_SANDBOX_ID;
    }
}
