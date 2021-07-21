<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;

class ProcessUnreviewedSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'antifraud:unreviewed-sales';

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
        $sales = Sale::with('transactions')
            ->whereIn(
                'id',
                [823061, 1082507, 1108115, 1108136, 1108153, 1115551, 1119980, 1130598, 1130699, 1130748]
            )->get();

        foreach ($sales as $sale) {
            $this->line('saleId: ' . $sale->id);
            $this->cancelSale($sale);
        }
        return 0;
    }

    public function cancelSale($sale)
    {
        $sale->update(
            [
                "status" => Sale::STATUS_CANCELED_ANTIFRAUD
            ]
        );
        $sale->save();

        foreach ($sale->transactions as $transaction) {
            $transaction->update(
                [
                    'status_enum' => Transaction::STATUS_CANCELED_ANTIFRAUD,
                    'status'      => 'canceled_antifraud'
                ]
            );
        }

        // set to upsell the same sale status
        $upsells = $sale->upsells->where('status', Sale::STATUS_IN_REVIEW);
        if ($upsells->count()) {
            foreach ($upsells as $upsell) {
                $this->cancelSale($upsell);
            }
        }
    }
}
