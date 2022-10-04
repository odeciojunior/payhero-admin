<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Entities\Gateway;
use Modules\Core\Abstracts\GatewayServicesAbstract;
class Safe2PayService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [
            Gateway::SAFE2PAY_PRODUCTION_ID,
            Gateway::SAFE2PAY_SANDBOX_ID
        ];

        $this->gatewayName = 'Vega';

        $this->companyColumnBalance = 'vega_balance';
    }

    public function getGatewayId():int
    {
        return foxutils()->isProduction()?Gateway::SAFE2PAY_PRODUCTION_ID:Gateway::SAFE2PAY_SANDBOX_ID;
    }

}
