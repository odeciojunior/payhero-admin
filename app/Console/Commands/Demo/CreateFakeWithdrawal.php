<?php

namespace App\Console\Commands\Demo;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\Gateways\Safe2PayService;
use Vinkla\Hashids\Facades\Hashids;

class CreateFakeWithdrawal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:create-fake-withdrawal';

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
        Config::set('database.default', 'demo');

        $gatewayService = new Safe2PayService();

        $gatewayService->setCompany(Company::find(Company::DEMO_ID));
        $balance =  $gatewayService->getAvailableBalance();

        if ($balance > 0) {
            $gatewayService->existsBankAccountApproved();
            $withdrawal = $gatewayService->createWithdrawal(mt_rand(5000, round($balance / 2)));

            if (!empty($withdrawal)) {
                $withdrawal->update([
                    'status' => Withdrawal::STATUS_TRANSFERRED
                ]);

                Transfer::create(
                    [
                        'user_id' => User::DEMO_ID,
                        'company_id' => $withdrawal->company_id,
                        'value' => $withdrawal->value,
                        'type' => 'out',
                        'type_enum' => Transfer::TYPE_OUT,
                        'reason' => 'Saque #'.Hashids::encode($withdrawal->id),
                        'gateway_id' => Gateway::SAFE2PAY_PRODUCTION_ID,
                    ]
                );
            }
        }
    }
}
