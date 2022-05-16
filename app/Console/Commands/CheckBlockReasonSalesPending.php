<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\Gateways\Safe2PayService;

class CheckBlockReasonSalesPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        $pendingBlockSales = BlockReasonSale::with('sale')
                                            ->where('status', BlockReasonSale::STATUS_PENDING_BLOCK);
        
        foreach ($pendingBlockSales->cursor() as $pendingBlockSale) {
            $transaction = Transaction::where('sale_id', $pendingBlockSale->sale_id)
                                        ->where('type', Transaction::TYPE_PRODUCER)
                                        ->first();

            $safe2payService = new Safe2PayService();
            $safe2payService->setCompany($transaction->company);

            $availableBalance = $safe2payService->getAvailableBalance();
            $pendingBalance = $safe2payService->getPendingBalance();
            $safe2payService->applyBlockedBalance($availableBalance, $pendingBalance);
            
            if($availableBalance + $pendingBalance > $transaction->value) {
                $pendingBlockSale->update(['status' => BlockReasonSale::STATUS_BLOCKED]);
            }
        }
    }

}
