<?php

namespace App\Console\Commands\Demo;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\CheckoutPlan;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Project;

class CreateFakeCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:create-fake-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    
    protected $project = null;
    protected $checkout = null;

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
        /* WARNING
        APÓS ENTRAR EM PRODUÇÃO SÓ DEVE GERAR 1 POR VEZ
        */

        Config::set('database.default', 'demo');
        
        try{
            $attemps = 50;
            $counter = 1;
        
            do{
                $this->createCheckout()
                ->createCheckoutPlan();
                $this->line($counter.'/'.$attemps);
                $counter++;

            }while($counter <= $attemps);
            
        }catch (Exception $e) {
            report($e);            
        }
    }

    public function createCheckout(){
        
        $this->project = DB::table('projects')->select('id')->inRandomOrder()->first();
        $checkoutConfig = DB::table('checkout_configs')->select('checkout_type_enum')->where('project_id',$this->project->id)->first();
        
        $this->checkout = Checkout::factory()
        ->count(1)
        ->create([                
            'project_id'=>$this->project->id,
            'template_type'=>(int)$checkoutConfig->checkout_type_enum
        ])->first();
        
        return $this;
    }

    public function createCheckoutPlan()
    {
        $plans = Plan::with(['productsPlans.product'])
        ->where('project_id',$this->project->id)
        ->inRandomOrder()->limit(Rand(1,3))->get();

        foreach($plans as $plan){            
            CheckoutPlan::factory(1)->for($this->checkout)->create([
                'plan_id'=>$plan->id,
                'amount'=>1,
            ]);            
        }

        return $this;
    }
}
