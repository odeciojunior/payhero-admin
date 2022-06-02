<?php

namespace App\Console\Commands\Demo;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Services\Gateways\Safe2PayService;

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
        $gatewayService = new Safe2PayService();

        $gatewayService->setCompany(Company::find(Company::DEMO_ID));
        $balance =  $gatewayService->getAvailableBalance();
        
        if($balance>0){
            $gatewayService->existsBankAccountApproved();
            $gatewayService->createWithdrawal(mt_rand(5000,$balance));
        }
    }
}
