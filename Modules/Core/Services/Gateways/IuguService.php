<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Entities\Gateway;
use Modules\Core\Abstracts\GatewayServicesAbstract;

class IuguService extends GatewayServicesAbstract
{
    public function __construct()
    {
        $this->gatewayIds = [
            Gateway::IUGU_PRODUCTION_ID,
            Gateway::IUGU_SANDBOX_ID
        ];

        $this->gatewayName = 'Vega';

        $this->companyColumnBalance = 'vega_balance';
    }

    public function getGatewayId():int
    {
        return foxutils()->isProduction()?Gateway::IUGU_PRODUCTION_ID:Gateway::IUGU_SANDBOX_ID;
    }
}
