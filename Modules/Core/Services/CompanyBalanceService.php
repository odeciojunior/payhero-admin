<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Interfaces\Statement;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Core\Services\Gateways\Safe2PayService;

/**
 * Class CompanyService
 * @package Modules\Core\Services
 */
class CompanyBalanceService
{
    private Company $company;

    private $defaultGateways = [
        Safe2PayService::class,
        AsaasService::class,
        GetnetService::class,
        GerencianetService::class,
        CieloService::class,
    ];

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function getResumes() : array
    {
        $gatewaysBalances = [];

        $totalAvailable = 0;
        foreach($this->defaultGateways as $gatewayClass) 
        {
            $gatewayService = app()->make($gatewayClass);
            $gatewayService->setCompany($this->company);

            $gatewayResume = $gatewayService->getResume();
            if(!empty($gatewayResume)) {
                $gatewaysBalances[] = $gatewayResume;
                $totalAvailable += intval($gatewayResume['total_available']);
            }
        }

        $gatewaysBalances['total_gateways_available'] = foxutils()->formatMoney($totalAvailable / 100);

        return $gatewaysBalances;
    }

    public function getAcquirers()
    {
        $gatewayIds = [];
        foreach($this->defaultGateways as $gatewayClass) {
            $gatewayService = app()->make($gatewayClass);
            $gatewayService->setCompany($this->company);
            $gatewayAvailable = $gatewayService->getGatewayAvailable();
            if(!empty($gatewayAvailable)) {                
                $gatewayIds = array_merge($gatewayIds,$gatewayAvailable);                
            }
        }
        return $gatewayIds;
    }

}
