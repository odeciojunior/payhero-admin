<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Entities\Gateway;
use Modules\Core\Abstracts\GatewayServicesAbstract;

class VegaService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [
            Gateway::SAFE2PAY_PRODUCTION_ID, Gateway::SAFE2PAY_SANDBOX_ID,
            Gateway::IUGU_PRODUCTION_ID, Gateway::IUGU_SANDBOX_ID,
            Gateway::VEGA_PRODUCTION_ID, Gateway::VEGA_SANDBOX_ID
        ];

        $this->gatewayName = 'Vega';

        $this->companyColumnBalance = 'vega_balance';
    }

    public function getGatewayId():int
    {
        return foxutils()->isProduction()?Gateway::VEGA_PRODUCTION_ID:Gateway::VEGA_SANDBOX_ID;
    }
}
