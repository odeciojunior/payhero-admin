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
        // $asaasService =  (new AsaasService())->setCompany($company);
        // $this->line($asaasService->getBlockedBalance());
        // $this->line($asaasService->getBlockedBalance2());



        //$queries = DB::getQueryLog();
        //dd($queries);
        //$this->line($asaasService->getAvailableBalanceWithoutBlocking());
        //$safe2PayService =  (new Safe2PayService())->setCompany($company);

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


        //$this->line($safe2PayService->getBlockedBalance());
        //$this->line($safe2PayService->getBlockedBalance2());


        // $transaction = Transaction::find(3442608);

        // dd($transaction->getDate($transaction->created_at));

        // $transaction1 = Transaction::whereHas('category', function($q) {
        //     $q->where('project_id', 1);
        // })->get();

        // $transaction2 = Transaction::select('transactions.*')
        // ->join('categories', 'transactions.category_id', '=', 'categories.id')
        // ->where('categories.project_id', 1)
        // ->get();

        $this->line('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

        // Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        // Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));
    }
}
