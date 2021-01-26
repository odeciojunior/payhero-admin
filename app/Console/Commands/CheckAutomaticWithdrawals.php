<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Entities\WithdrawalSettings;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\FoxUtils;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Withdrawals\Services\WithdrawalsService;
use Vinkla\Hashids\Facades\Hashids;

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
        $withdrawalsSettings = $withdrawalSettingsModel->where('company_id', 471)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

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
                    if ($availableBalance > $withdrawalValue) {
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
                    dd($withdrawal);
                    event(new WithdrawalRequestEvent($withdrawal));
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }


        }

        dd('check:automatic-withdrawals', count($withdrawalsSettings));
        // +3 horas
    }
}
