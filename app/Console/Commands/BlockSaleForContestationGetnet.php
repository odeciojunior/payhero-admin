<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\BlockReason;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class BlockSaleForContestationGetnet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "getnet:block-sale-for-contestation";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

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
        $sales = Sale::select("sales.id")
            ->join("sale_contestations as c", "sales.id", "=", "c.sale_id")
            ->leftJoin("block_reason_sales as b", "sales.id", "=", "b.sale_id")
            ->where("sales.gateway_id", Gateway::GETNET_PRODUCTION_ID)
            ->where("sales.status", Sale::STATUS_APPROVED)
            ->where("c.status", SaleContestation::STATUS_IN_PROGRESS)
            ->whereNull("b.sale_id")
            ->get();

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, count($sales));
        $progress->start();

        foreach ($sales as $sale) {
            BlockReasonSale::create([
                "sale_id" => $sale->id,
                "blocked_reason_id" => BlockReason::IN_DISPUTE,
                "status" => BlockReasonSale::STATUS_BLOCKED,
                "observation" => "Em disputa",
            ]);

            $progress->advance();
        }

        $progress->finish();
    }
}
