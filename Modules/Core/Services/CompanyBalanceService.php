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
    public const AVAILABLE_BALANCE = 'getAvailableBalance';
    public const PENDING_BALANCE = 'getPendingBalance';
    public const BLOCKED_BALANCE = 'getBlockedBalance';
    public const BLOCKED_PENDING_BALANCE = 'getBlockedBalancePending';
    public const PENDING_DEBT_BALANCE = 'getPendingDebtBalance';

    private Company $company;
    private ?Statement $gatewayStatementService;

    private $defaultGateways = [
        AsaasService::class,
        GetnetService::class,
        GerencianetService::class,
        CieloService::class,
        Safe2PayService::class
    ];

    public function __construct(Company $company, Statement $gatewayStatementService = null)
    {
        $this->company = $company;
        $this->gatewayStatementService = $gatewayStatementService;
    }

    public function getBalance($method) : int
    {
        if(!empty($this->gatewayStatementService)) {
            $this->gatewayStatementService->setCompany($this->company);
            return $this->gatewayStatementService->$method();
        }
        else {
            $balances = [];
            foreach($this->defaultGateways as $gatewayClass) {
                $gateway = app()->make($gatewayClass);
                $gateway->setCompany($this->company);
                $balances[] = $gateway->$method();
            }
            return array_sum($balances);
        }
    }

    public function hasEnoughBalanceToRefund(Sale $sale): bool
    {
        return $this->gatewayStatementService->hasEnoughBalanceToRefund($sale);
    }

    public function getResumes() : array
    {
        $gatewaysBalances = [];

        // if (!auth()->user()->show_old_finances) {
        //     array_pop($this->defaultGateways);
        // }

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
