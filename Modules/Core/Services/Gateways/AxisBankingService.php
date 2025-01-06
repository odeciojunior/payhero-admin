<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Entities\Gateway;
use Modules\Core\Abstracts\GatewayServicesAbstract;

class AxisBankingService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [Gateway::AXISBANKING_PRODUCTION_ID, Gateway::AXISBANKING_SANDBOX_ID];

        $this->gatewayName = "Vega";

        $this->companyColumnBalance = "vega_balance";
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::AXISBANKING_PRODUCTION_ID : Gateway::AXISBANKING_SANDBOX_ID;
    }
}
