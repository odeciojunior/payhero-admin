<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleGatewayRequest;
use Modules\Core\Services\SaleService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $requests = SaleGatewayRequest::whereHas('sale',function($query){
            $query->where('payment_method',Sale::CREDIT_CARD_PAYMENT);
        })->where('gateway_id',Gateway::ASAAS_PRODUCTION_ID)->count();

        foreach($requests as $request){
            
        }

    }

}
