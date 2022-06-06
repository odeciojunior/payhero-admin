<?php

namespace App\Console\Commands\Demo;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;

class ChangeStatusCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:change-status-checkout';

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

        $checkouts = DB::table('checkouts as c')->select('c.id')->where('c.status_enum',Checkout::STATUS_ACCESSED)
        ->leftJoin('sales as s','c.id','=','s.checkout_id')
        ->whereNull('s.id')->where('c.created_at','<=',Carbon::now()->subDay())
        ->get();
        
        foreach ($checkouts as $checkout)
        {
            $status = $this->getRandomStatus();
            
            Checkout::find($checkout->id)->update([
                'status'=>$status['status'],
                'status_enum'=>$status['enum']
            ]);
            
            $this->line('Atualizando checjout '.$checkout->id);
        }        
    }

    public function getRandomStatus(){
        $status = [
            ['enum'=>Checkout::STATUS_ABANDONED_CART,'status'=>'abandoned cart'],
            ['enum'=>Checkout::STATUS_RECOVERED,'status'=>'recovered']
        ];
        return Arr::random($status);
    }
}
