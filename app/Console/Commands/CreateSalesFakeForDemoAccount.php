<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\CheckoutPlan;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Delivery;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;

class CreateSalesFakeForDemoAccount extends Command
{
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

        $this->generateCheckouts();
        
        /* script checkout
            $plan = Plan::inRandomOrder()->first();
            $plan->amount = 1;
            $plans[] = $plan;
            $project = Project::with('checkoutConfig')->find($plan->project_id);
            $checkoutConfig = $project->checkoutConfig;
            $totalValue = FoxUtils::onlyNumbers($plan['price']);
            $subTotal = $totalValue;
    
            $productsPlans = ProductPlan::with('product')->where('plan_id', $plan->id)->get()->toArray();
            $productsQuantity = 0;
            foreach ($productsPlans as &$productPlan) {
                $productsQuantity += $productPlan['amount'];
            }
    
            $producer = UserProject::with(['company', 'user'])
                    ->where('project_id', $project->id)
                    ->where('type_enum', UserProject::TYPE_PRODUCER_ENUM)
                    ->first();
            */
    }

    public function generateCheckouts(){
        try {
            $plan = Plan::inRandomOrder()->first();
            $project = Project::with('checkoutConfig')->find($plan->project_id);
            $checkoutConfig = $project->checkoutConfig;

            DB::beginTransaction();
            Checkout::factory()
            ->state([                
                'project_id'=>$project->id,
                'template_type'=>$checkoutConfig->checkout_type_enum
            ])
            ->has(CheckoutPlan::factory()->state(['plan_id'=>$plan->id])->count(1))
            ->count(10)
            ->create();
            DB::commit();

        } catch (Exception $e) {
            \Log::info($e->getMessage());
            DB::rollBack();
        }   
    }

    public function generateSales(){
        $customer = Customer::factory()
        ->count(1)
        ->has(Delivery::factory()->count(1))
        ->create();

        Sale::factory()->count(1)->for($customer)->create();
    }
}
