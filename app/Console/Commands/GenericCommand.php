<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        dd(DB::statement('update transactions inner join sales on transactions.sale_id = sales.id set transactions.gateway_id = sales.gateway_id'));
        // $totalCount = Transaction::whereNull('gateway_id')->count();
        // $currentCount = 0;

        // Transaction::with('sale')->whereNull('gateway_id')->orderBy('id', 'desc')
        //             ->chunk(100, function ($transactions) use($totalCount, $currentCount) {

        //                 foreach($transactions as $transaction) {
        //                     $this->line("Atualizando transaction {$currentCount} de {$totalCount} ");

        //                     $transaction->update([
        //                         'gateway_id' => $transaction->sale->gateway_id
        //                     ]);

        //                     $currentCount++;
        //                 }
        //             });
    }
}


//update transactions inner join sales on transactions.sale_id = sales.id set transactions.gateway_id = sales.gateway_id;