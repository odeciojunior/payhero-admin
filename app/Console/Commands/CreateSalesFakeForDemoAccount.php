<?php

namespace App\Console\Commands;

use App\Traits\DemoPaymentFlowTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Modules\Core\Entities\Company;

class CreateSalesFakeForDemoAccount extends Command
{
    use DemoPaymentFlowTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo-account:create-sales-fake';

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
        $attemps = 3;
        $counter = 1;
        do{
            $this->createCheckout()
                ->preparePlans()        
                ->prepareData()
                ->checkAutomaticDiscount()
                ->checkDiscountCoupon()
                ->setCustomer()
                ->setShipping()    
                ->checkProgressiveDiscount()        
                ->calculateValues()
                ->setSale()
                ->executePayment()
                ->setTransactions();
            $counter++;

        }while($counter <= $attemps);
    }    

}
