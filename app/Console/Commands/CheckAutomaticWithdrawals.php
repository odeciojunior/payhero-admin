<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\WithdrawalSettings;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\CompanyService;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Withdrawals\Services\WithdrawalsService;

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $service = new WithdrawalService();
        $withdrawalSettingsModel = new WithdrawalSettings();
        $withdrawalsSettings = $withdrawalSettingsModel->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        foreach ($withdrawalsSettings as $settings) {

            try {
                DB::beginTransaction();
                $companyModel = new Company();
                $companyService = new CompanyService();
                $company = $companyModel->find($settings->company->id);

                $availableBalance = $companyService->getAvailableBalance(
                    $company,
                    CompanyService::STATEMENT_AUTOMATIC_LIQUIDATION_TYPE
                );

                $withdrawalValue = null;
                if ($settings->rule == WithdrawalSettings::RULE_AMOUNT) {
                    if ($availableBalance >= $settings->amount) {
                        $withdrawalValue = $availableBalance;
                    }
                } else if ($settings->rule == WithdrawalSettings::RULE_PERIOD) {
                    if ($settings->frequency == WithdrawalSettings::FREQUENCY_DAILY) {
                        $withdrawalValue = $availableBalance;
                    } else if ($settings->frequency == WithdrawalSettings::FREQUENCY_WEEKLY && $settings->weekday == date('w')) {
                        $withdrawalValue = $availableBalance;
                    } else if ($settings->frequency == WithdrawalSettings::FREQUENCY_MONTHLY && $settings->day == date('d')) {
                        $withdrawalValue = $availableBalance;
                    }
                }

                if ($withdrawalValue > 0) {
                    $withdrawal = $service->requestWithdrawal($company, $withdrawalValue);
                    event(new WithdrawalRequestEvent($withdrawal));
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }

        return 0;
    }
}
