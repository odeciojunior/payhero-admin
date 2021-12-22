<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\SaleService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $sale = Sale::find(1409337);
        $saleService = new SaleService();
        $saleTax = 0;
        
            $cashbackValue = !empty($sale->cashback) ? $sale->cashback->value:0;
            $saleTax = $saleService->getSaleTaxRefund($sale,$cashbackValue);
        dd($saleTax);
    }

}
