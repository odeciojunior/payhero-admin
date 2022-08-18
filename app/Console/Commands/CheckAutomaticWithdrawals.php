<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Entities\WithdrawalSettings;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\Safe2PayService;

class CheckAutomaticWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "check:automatic-withdrawals";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Gera o saque dos usuário que possuem rotinas de saque automático habilitado";

    private $defaultGateways = [
        AsaasService::class,
        //GetnetService::class,
        GerencianetService::class,
        Safe2PayService::class
        //CieloService::class,
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $withdrawalsSettings = WithdrawalSettings::
            whereNull("deleted_at")
            ->orderBy("id", "DESC")
            ->get();

        settings()
            ->group("withdrawal_request")
            ->set("withdrawal_request", false);

        foreach ($withdrawalsSettings as $settings) {
            try {
                DB::beginTransaction();
                $company = Company::find($settings->company->id);

                //It only generates the automatic withdrawal if the account is active
                if ($company->user->status == User::STATUS_ACTIVE) {

                    foreach ($this->defaultGateways as $gatewayClass) {
                        $gatewayService = new $gatewayClass();
                        $gatewayService->setCompany($company);

                        $availableBalance = $gatewayService->getAvailableBalance();
                        $pendingBalance = $gatewayService->getPendingBalance();
                        (new CompanyService())->applyBlockedBalance($gatewayService, $availableBalance, $pendingBalance);

                        $withdrawalValue = $this->getAvailableBalance($settings, $availableBalance);

                        if ($withdrawalValue >= 10000) {
                            if ($gatewayService->existsBankAccountApproved()) {
                                $withdrawal = $gatewayService->createWithdrawal($withdrawalValue);
                                if ($withdrawal) {
                                    event(new WithdrawalRequestEvent($withdrawal));
                                }
                            }
                        }
                    }
                }

                DB::commit();
            } catch (Exception $e) {
                report($e);
                DB::rollBack();
            }
        }

        settings()
            ->group("withdrawal_request")
            ->set("withdrawal_request", true);

        return 0;
    }

    public function getAvailableBalance($settings, $availableBalance)
    {
        $withdrawalValue = 0;
        if ($settings->rule == WithdrawalSettings::RULE_AMOUNT) {
            if ($availableBalance >= $settings->amount) {
                $withdrawalValue = $availableBalance;
            }
        } elseif ($settings->rule == WithdrawalSettings::RULE_PERIOD) {
            if ($settings->frequency == WithdrawalSettings::FREQUENCY_DAILY) {
                $withdrawalValue = $availableBalance;
            } elseif ($settings->frequency == WithdrawalSettings::FREQUENCY_WEEKLY && $settings->weekday == date("w")) {
                $withdrawalValue = $availableBalance;
            } elseif ($settings->frequency == WithdrawalSettings::FREQUENCY_MONTHLY) {
                $isFebruary = date("m") == 2;
                $isFebruaryLastDay = $isFebruary && in_array(date("d"), [28, 29]);
                if ($settings->day == date("d") || ($settings->day == 30 && $isFebruaryLastDay)) {
                    $withdrawalValue = $availableBalance;
                }
            }
        }
        return $withdrawalValue;
    }
}
