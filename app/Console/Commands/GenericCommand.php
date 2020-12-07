<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Sale;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $sales = Sale::where('gateway_id', 15)->orderBy('id', 'desc')->get();

        foreach($sales as $sale) {
            Redis::connection('redis-statement')->set("sale:has:tracking:{$sale->id}", $sale->has_valid_tracking);
        }

        // foreach($sales as $sale) {
        //     $this->line("Venda {$sale->id} -> " . Redis::connection('redis-statement')->get("sale:has:tracking:{$sale->id}"));
        // }

    }
}


