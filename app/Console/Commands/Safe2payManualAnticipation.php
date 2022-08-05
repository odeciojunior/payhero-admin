<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\Gateways\CheckoutGateway;

class Safe2payManualAnticipation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'safe2pay:manual-anticipation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Anticipa as vendas no safe2pay';

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
        //DEATIVADO ATE AVISO PREVIO 
        // $attemps = 0;
        // $maxAttemps = 2;
        // $service = new CheckoutGateway(Gateway::SAFE2PAY_PRODUCTION_ID);
        
        // while($attemps < $maxAttemps){
            
        //     $response = $service->safe2payAnticipation(); 
        //     $attemps++;
            
        //     if(empty($response) || !isset($response->HasError) || $response->HasError){ 
        //         if($attemps == $maxAttemps){
        //             report(new Exception("Error Safe2pay anticipation: ".($response->Error??'não informado')));
        //         }
        //         continue;
        //     }

        //     break;
        // }
    }
}
