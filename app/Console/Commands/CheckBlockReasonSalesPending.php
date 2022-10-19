<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\Gateways\VegaService;

class CheckBlockReasonSalesPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "check:block-sales";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Verifica vendas pendentes de bloqueio";

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
        $pendingBlockSales = BlockReasonSale::with("sale")->where("status", BlockReasonSale::STATUS_PENDING_BLOCK);

        foreach ($pendingBlockSales->cursor() as $pendingBlockSale) {
            $transaction = Transaction::where("sale_id", $pendingBlockSale->sale_id)
                ->where("type", Transaction::TYPE_PRODUCER)
                ->first();

            $vegaService = new VegaService();
            $vegaService->setCompany($transaction->company);

            $availableBalance = $vegaService->getAvailableBalance();
            $pendingBalance = $vegaService->getPendingBalance();

            (new CompanyService())->applyBlockedBalance($vegaService, $availableBalance, $pendingBalance);

            if ($availableBalance + $pendingBalance >= $transaction->value) {
                $pendingBlockSale->update(["status" => BlockReasonSale::STATUS_BLOCKED]);
            }
        }
    }
}
