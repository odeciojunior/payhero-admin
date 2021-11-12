<?php

namespace App\Console\Commands;

use Modules\Core\Entities\Sale;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Console\Command;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $sale = Sale::find(1348648);
        //1156391
        $saleModel = Sale::where(
            [
                ['payment_method', '=', Sale::PIX_PAYMENT],
                ['status', '=', Sale::STATUS_APPROVED],
                //['customer_id', $sale->customer->id]
            ]
        )
            ->whereHas('customer', function($q) use($sale){
                $q->where('document', $sale->customer->document);
            })
            ->whereDate('start_date', \Carbon\Carbon::parse($sale->start_date)->format("Y-m-d"))->first();

        dd($saleModel);
    }

}
