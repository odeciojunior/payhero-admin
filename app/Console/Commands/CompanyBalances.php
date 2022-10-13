<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBalance;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Core\Services\Gateways\Safe2PayService;

class CompanyBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "company-balance";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command para atualizar o saldo disponivel";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $companies = Company::where("address_document_status", Company::STATUS_APPROVED)
                ->where("contract_document_status", Company::STATUS_APPROVED)
                ->get();
            foreach ($companies as $company) {
                $companyBalance = CompanyBalance::where("company_id", $company->id)->first();
                if (empty($companyBalance->id)) {
                    $companyBalance = new CompanyBalance();
                    $companyBalance->company_id = $company->id;
                }

                $vegaService = new Safe2PayService();
                $vegaService->setCompany($company);
                $companyBalance->vega_available_balance = $vegaService->getAvailableBalance();
                $companyBalance->vega_pending_balance = $vegaService->getPendingBalance();
                $companyBalance->vega_blocked_balance = $vegaService->getBlockedBalance();
                $companyBalance->vega_total_balance =
                    $companyBalance->vega_available_balance + $companyBalance->vega_pending_balance;

                $asaasService = new AsaasService();
                $asaasService->setCompany($company);
                $companyBalance->asaas_available_balance = $asaasService->getAvailableBalance();
                $companyBalance->asaas_pending_balance = $asaasService->getPendingBalance();
                $companyBalance->asaas_blocked_balance = $asaasService->getBlockedBalance();
                $companyBalance->asaas_total_balance =
                    $companyBalance->asaas_available_balance + $companyBalance->asaas_pending_balance;

                $cieloService = new CieloService();
                $cieloService->setCompany($company);
                $companyBalance->cielo_available_balance = $cieloService->getAvailableBalance();
                $companyBalance->cielo_pending_balance = $cieloService->getPendingBalance();
                $companyBalance->cielo_blocked_balance = $cieloService->getBlockedBalance();
                $companyBalance->cielo_total_balance =
                    $companyBalance->cielo_available_balance + $companyBalance->cielo_pending_balance;

                $getNetService = new GetnetService();
                $getNetService->setCompany($company);
                $companyBalance->getnet_available_balance = $getNetService->getAvailableBalance();
                $companyBalance->getnet_pending_balance = $getNetService->getPendingBalance();
                $companyBalance->getnet_blocked_balance = $getNetService->getBlockedBalance();
                $companyBalance->getnet_total_balance =
                    $companyBalance->getnet_available_balance + $companyBalance->getnet_pending_balance;

                $gerenciaNetService = new GerencianetService();
                $gerenciaNetService->setCompany($company);
                $companyBalance->gerencianet_available_balance = $gerenciaNetService->getAvailableBalance();
                $companyBalance->gerencianet_pending_balance = $gerenciaNetService->getPendingBalance();
                $companyBalance->gerencianet_blocked_balance = $gerenciaNetService->getBlockedBalance();
                $companyBalance->gerencianet_total_balance =
                    $companyBalance->gerencianet_available_balance + $companyBalance->gerencianet_pending_balance;

                $companyBalance->total_balance =
                    $companyBalance->vega_total_balance +
                    $companyBalance->asaas_total_balance +
                    $companyBalance->cielo_total_balance +
                    $companyBalance->getnet_total_balance +
                    $companyBalance->gerencianet_total_balance;

                $companyBalance->save();
            }
        } catch (Exception $e) {
            dd($e->getMessage());
            report($e);
        }
    }

    private function companyBalance(Company $company)
    {
        $companyBalance = CompanyBalance::where("company_id", $company->id)->get();
        if (empty($companyBalance->id)) {
            $companyBalance = new CompanyBalance();
            $companyBalance->company_id = $company->id;
            return $companyBalance;
        } else {
            dd("xxx");
            return $companyBalance;
        }
    }
}
