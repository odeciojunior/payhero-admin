<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Company;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Core\Services\Gateways\IuguService;
use Modules\Core\Services\Gateways\VegaService;

/**
 * Class CompanyService
 * @package Modules\Core\Services
 */
class CompanyBalanceService
{
    private Company $company;

    private $defaultGateways = [VegaService::class, IuguService::class];

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function getResumes(): array
    {
        $gatewaysBalances = [];

        $totalAvailable = 0;
        foreach ($this->defaultGateways as $gatewayClass) {
            $gatewayService = app()->make($gatewayClass);
            $gatewayService->setCompany($this->company);

            $gatewayResume = $gatewayService->getResume();
            if (!empty($gatewayResume)) {
                $gatewaysBalances[] = $gatewayResume;
                $totalAvailable += intval($gatewayResume["total_available"]);
            }
        }

        $gatewaysBalances["total_gateways_available"] = foxutils()->formatMoney($totalAvailable / 100);

        return $gatewaysBalances;
    }

    public function getResumeTotals($request)
    {
        $gatewaysBalances = [];
        $totalAvailable = 0;
        $totalBalance = 0;

        foreach ($this->defaultGateways as $gatewayClass) {
            $gatewayService = app()->make($gatewayClass);
            $gatewayService->setCompany($this->company);
            $gatewayResume = $gatewayService->getResume();

            if (!empty($gatewayResume)) {
                $gatewaysBalances[] = $gatewayResume;
                $totalAvailable += intval($gatewayResume["total_available"]);
                $totalBalance += intval($gatewayResume["total_balance"]);
            }
        }

        // Checks if the request has the 'is_mobile' parameter
        if ($request->has("is_mobile")) {
            // Formats gateway values
            foreach ($gatewaysBalances as &$gatewayBalance) {
                foreach ($gatewayBalance as &$data) {
                    $data = is_int($data) ? number_format(intval($data) / 100, 2, ",", ".") : $data;
                }
            }

            // Formats the total available
            $totalAvailable = is_int($totalAvailable)
                ? number_format(intval($totalAvailable) / 100, 2, ",", ".")
                : $totalAvailable;

            // Formats the total balance
            $totalBalance = is_int($totalBalance)
                ? number_format(intval($totalBalance) / 100, 2, ",", ".")
                : $totalBalance;
        }

        return [
            "data" => [
                "gateways_balances" => $gatewaysBalances,
                "total_gateways_available" => $totalAvailable,
                "total_balance" => $totalBalance,
            ],
        ];
    }

    public function getAcquirers()
    {
        $gatewayIds = [];
        foreach ($this->defaultGateways as $gatewayClass) {
            $gatewayService = app()->make($gatewayClass);
            $gatewayService->setCompany($this->company);
            $gatewayAvailable = $gatewayService->getGatewayAvailable();
            if (!empty($gatewayAvailable)) {
                $gatewayIds = array_merge($gatewayIds, $gatewayAvailable);
            }
        }
        return $gatewayIds;
    }
}
