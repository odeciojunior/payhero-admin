<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\WithdrawalSettings;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Withdrawals\Services\WithdrawalService;

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
    protected $description = 'Command description';

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
        $service = new WithdrawalService();
        $withdrawalSettingsModel = new WithdrawalSettings();
        $withdrawalsSettings = $withdrawalSettingsModel->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        settings()->group('withdrawal_request')->set('withdrawal_request', false);

        foreach ($withdrawalsSettings as $settings) {

            try {
                DB::beginTransaction();
                $companyModel = new Company();
                $company = $companyModel->find($settings->company->id);
                $companyService = new CompanyBalanceService($company, (new GetnetService));

                $availableBalance = $companyService->getBalance(CompanyBalanceService::AVAILABLE_BALANCE);

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

                if ($withdrawalValue >= 10000) {
                    $withdrawal = $service->requestWithdrawal($company, $withdrawalValue);
                    event(new WithdrawalRequestEvent($withdrawal));
                }

                DB::commit();
            } catch (\Exception $e) {
                report($e);
                DB::rollBack();
            }
        }

        settings()->group('withdrawal_request')->set('withdrawal_request', true);

        return 0;
    }
}
