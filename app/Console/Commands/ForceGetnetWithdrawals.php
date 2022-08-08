<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\Gateways\GetnetService;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class ForceGetnetWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "getnet:force-withdrawals";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $getnetService = new GetnetService();

            $companies = Company::whereHas("transactions", function ($q) {
                $q->where("gateway_id", Gateway::GETNET_PRODUCTION_ID);
            })->get();

            $output = new ConsoleOutput();
            $progress = new ProgressBar($output, count($companies));
            $progress->start();

            $count = 0;
            $pendingDebtsSum = 0;
            foreach ($companies as $company) {
                $progress->advance();

                $getnetService->setCompany($company);
                $availableBalance = 0;
                $pendingDebts = $getnetService->getPendingDebtBalance();
                if ($pendingDebts > 0) {
                    $availableBalance = $getnetService->getAvailableBalance();
                }

                if ($pendingDebts > 0 && $availableBalance > $pendingDebts) {
                    Log::info($company->user->name . " - " . $company->fantasy_name);
                    Log::info("Saldo disponível: " . foxutils()->formatMoney($availableBalance / 100));
                    Log::info("Débitos pendentes: " . foxutils()->formatMoney($pendingDebts / 100));
                    $count++;
                    $pendingDebtsSum += $pendingDebts;
                }
            }

            $progress->finish();

            dd($count, $pendingDebtsSum);

            // foreach($this->getSubsellerIds() as $subsellerId) {

            //     $credential = GatewaysCompaniesCredential::with('company')->where('gateway_subseller_id', $subsellerId)->first();
            //     $company = $credential->company;
            //     $getnetService = new GetnetService();
            //     $getnetService->setCompany($company);

            //     $availableBalance = foxutils()->formatMoney($getnetService->getAvailableBalance() / 100);
            //     $pendingBalance = foxutils()->formatMoney($getnetService->getPendingBalance() / 100);
            //     $blockedBalance = foxutils()->formatMoney($getnetService->getBlockedBalance() / 100);
            //     $debtsBalance = foxutils()->formatMoney($getnetService->getPendingDebtBalance() / 100);

            //     $this->line("----------------------------------------");
            //     $this->line("--->>   Empresa " . $company->fantasy_name);
            //     $this->line("SubsellerId " . $subsellerId);
            //     $this->line("Saldo disponível: {$availableBalance}");
            //     $this->line("Saldo pendente: {$pendingBalance}");
            //     $this->line("Saldo bloqueado: {$blockedBalance}");
            //     $this->line("Débitos: {$debtsBalance}");
            //     $this->line("----------------------------------------");

            //     Log::info("----------------------------------------");
            //     Log::info("--->>   Empresa " . $company->fantasy_name);
            //     Log::info("SubsellerId " . $subsellerId);
            //     Log::info("Saldo disponível: {$availableBalance}");
            //     Log::info("Saldo pendente: {$pendingBalance}");
            //     Log::info("Saldo bloqueado: {$blockedBalance}");
            //     Log::info("Débitos: {$debtsBalance}");
            //     Log::info("----------------------------------------");

            // }
        } catch (Exception $e) {
            report($e);
        }
    }

    public function getSubsellerIds()
    {
        return [
            700122291,
            700122361,
            700130070,
            700132156,
            700139912,
            700164212,
            700226199,
            700273529,
            700122295,
            700122300,
            700129576,
            700162722,
            700168849,
            700284142,
            700122369,
            700147087,
            700154525,
            700204969,
            700226166,
            700301116,
            700302985,
            700308013,
            700313811,
            700122387,
            700129596,
            700144010,
            700148195,
            700153762,
            700157020,
            700175904,
            700242004,
            700242602,
            700270460,
            700122394,
            700139913,
            700145220,
            700159681,
            700161009,
            700176967,
            700248713,
            700270467,
            700285246,
            700291410,
            700362794,
            700122289,
            700122318,
            700122329,
            700173381,
            700176895,
            700232549,
            700239837,
            700277586,
            700324345,
            700122380,
            700122397,
            700122412,
            700142577,
            700239053,
            700276867,
            700287049,
            700305030,
            700343068,
            700120538,
            700122362,
            700122365,
            700122368,
            700139564,
            700144890,
            700160298,
            700225360,
            700329638,
        ];
    }
}
