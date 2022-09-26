<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;

class UnlockBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "balance:unlock";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sales = Sale::with("blockReasonsSale")
            ->whereHas("blockReasonsSale", function ($q) {
                $q->where("status", 1);
            })
            ->where("status", "!=", Sale::STATUS_APPROVED)
            ->get();

        foreach ($sales as $sale) {
            foreach ($sale->blockReasonsSale as $block) {
                if ($block->status != 2) {
                    $block->update([
                        "status" => 2,
                    ]);
                }
            }
        }
    }
}
