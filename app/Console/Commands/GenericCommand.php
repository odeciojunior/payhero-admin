<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\Safe2PayService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {

        $this->line('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        $company = Company::find(471);
        $this->line($company->id);

        $gatewayId = hashids_decode('BeYEwR3AdgdKykA');

        $companyService = new CompanyBalanceService($company, Gateway::getServiceById($gatewayId));

        $blockedBalance = $companyService->getBalance(CompanyBalanceService::BLOCKED_BALANCE);
        $blockedBalancePending = $companyService->getBalance(CompanyBalanceService::BLOCKED_PENDING_BALANCE);
        $pendingBalance = $companyService->getBalance(CompanyBalanceService::PENDING_BALANCE);
        $availableBalance = $companyService->getBalance(CompanyBalanceService::AVAILABLE_BALANCE);

        $blockedBalanceTotal = $blockedBalancePending + $blockedBalance;
        $totalBalance = $availableBalance + $pendingBalance + $blockedBalanceTotal;
        $pendingDebtBalance = $companyService->getBalance(CompanyBalanceService::PENDING_DEBT_BALANCE);


        $this->line('available_balance = '. foxutils()->formatMoney($availableBalance / 100));
        $this->line('total_balance = '. foxutils()->formatMoney($totalBalance / 100));
        $this->line('pending_balance = '. foxutils()->formatMoney($pendingBalance / 100));
        $this->line('blocked_balance = '. foxutils()->formatMoney($blockedBalanceTotal / 100));
        $this->line('pending_debt_balance = '. foxutils()->formatMoney($pendingDebtBalance / 100));

        $this->line('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));
    }
}
