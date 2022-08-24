<?php

namespace App\Console\Commands\Demo;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Modules\Core\Entities\Company;
use Modules\Core\Services\DemoAccount\DemoPaymentFlowTrait;

class CreateFakeSale extends Command
{
    use DemoPaymentFlowTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:create-fake-sale';

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

        $this->company = Company::find(Company::DEMO_ID);
        $isRandomData = false;
        $attemps = 1;
        $counter = 1;

        do{
            $this->nextIsUpsell = mt_rand(1,10)==7;
            do{
                $this->resetVars()
                    ->validateCheckoutLogs()
                    ->preparePlans()
                    ->prepareOrderBump()
                    ->prepareData()
                    ->checkAutomaticDiscount()
                    ->checkDiscountCoupon()
                    ->setCustomer()
                    ->setShipping()
                    ->checkProgressiveDiscount()
                    ->calculateValues()
                    ->setSale($isRandomData)
                    ->executePayment()
                    ->setTransactions()
                    ->setTranking();

                $this->upsellPreviousSaleId = $this->sale->id;

                if($this->isUpsell){
                    $this->isUpsell = false;
                }else{
                    $this->isUpsell = $this->nextIsUpsell;
                }

                $counter++;
            }while($this->isUpsell);

            $this->line("$counter/$attemps");
        }while($counter <= $attemps);


    }
}
