<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\TransactionsService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {

        $sales = Sale::with('transactions')
            ->where('project_id', 3722)
            ->where('status', Sale::STATUS_APPROVED)
            ->whereBetween('end_date', ['2021-04-28 00:00:00', '2021-07-15 23:59:59'])
            ->get();

        $transactionService = new TransactionsService();

        foreach ($sales as $sale) {

            $transaction = $sale->transactions->where('type', Transaction::TYPE_PRODUCER)->first();
            $transaction->tracking_required = false;
            $transaction->save();

            $transactionService->verifyAutomaticLiquidationTransactions($sale->id);
        }
    }
}


