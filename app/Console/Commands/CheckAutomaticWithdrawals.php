<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\WithdrawalSettings;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;

class CheckAutomaticWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:automatic-withdrawals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Gera o saque dos usuário que possuem rotinas de saque automático habilitado";

    private $defaultGateways = [
        AsaasService::class,
        GetnetService::class,
        GerencianetService::class,
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
        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        $service = new WithdrawalService();
        $withdrawalSettingsModel = new WithdrawalSettings();
        $withdrawalsSettings = $withdrawalSettingsModel->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        settings()->group('withdrawal_request')->set('withdrawal_request', false);

        foreach ($withdrawalsSettings as $settings) {

            try {
                DB::beginTransaction();
                $company = Company::find($settings->company->id);

                foreach($this->defaultGateways as $gatewayClass){

                    $gatewayService = new $gatewayClass;
                    $gatewayService->setCompany($company);

                    $availableBalance = $gatewayService->getAvailableBalance();
                    $withdrawalValue = $this->getAvailableBalance($settings,$availableBalance);

                    if ($withdrawalValue >= 10000) {
                        $withdrawal = $gatewayService->createWithdrawal($withdrawalValue);
                        if($withdrawal){
                            event(new WithdrawalRequestEvent($withdrawal));
                        }
                    }
                }

                DB::commit();
            } catch (Exception $e) {
                report($e);
                DB::rollBack();
            }
        }

        settings()->group('withdrawal_request')->set('withdrawal_request', true);

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));
        return 0;
    }

    public function getAvailableBalance($settings,$availableBalance)
    {
        $withdrawalValue = 0;
        if ($settings->rule == WithdrawalSettings::RULE_AMOUNT) {
            if ($availableBalance >= $settings->amount) {
                $withdrawalValue = $availableBalance;
            }
        } else if ($settings->rule == WithdrawalSettings::RULE_PERIOD) {
            if ($settings->frequency == WithdrawalSettings::FREQUENCY_DAILY) {
                $withdrawalValue = $availableBalance;
            } else if ($settings->frequency == WithdrawalSettings::FREQUENCY_WEEKLY && $settings->weekday == date('w')) {
                $withdrawalValue = $availableBalance;
            } else if ($settings->frequency == WithdrawalSettings::FREQUENCY_MONTHLY) {
                $isFebruary = (date('m') == 2);
                $isFebruaryLastDay = ($isFebruary && in_array(date('d'), [28, 29]));
                if ($settings->day == date('d') || ($settings->day == 30 && $isFebruaryLastDay)) {
                    $withdrawalValue = $availableBalance;
                }
            }
        }
        return $withdrawalValue;
    }
}
