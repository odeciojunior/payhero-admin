<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UnderAttack;

class UpdateUnderAttack extends Command
{
    protected $signature = "under-attack:update-card-declined";

    protected $description = "Card declined";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $start_date = Carbon::now()->endOfDay();
            $end_date = Carbon::now()
                ->subDay(15)
                ->startOfDay();
            $sales = Sale::selectRaw(
                'owner_id, COUNT(id) AS totalSales,
                            COUNT(CASE WHEN STATUS = 3 OR STATUS = 21 THEN 1 END) AS refusedCount,
                            COUNT(CASE WHEN STATUS = 3 OR STATUS = 21 THEN 1 END) > (COUNT(id) / 2 ) AS refused'
            )
                ->whereBetween("start_date", [$end_date, $start_date])
                ->where("payment_method", Sale::CREDIT_CARD_PAYMENT)
                ->groupBy("owner_id")
                ->get();

            $salesRefused = $sales->where("refused", true)->where("totalSales", ">", 30);
            UnderAttack::where("type", "CARD_DECLINED")->delete();

            if ($salesRefused) {
                foreach ($salesRefused as $saleRefuse) {
                    $refusedPercentage = number_format(
                        ($saleRefuse->refusedCount * 100) / $saleRefuse->totalSales,
                        2,
                        ",",
                        "."
                    );

                    $under_attack = new UnderAttack();
                    $under_attack->type = "CARD_DECLINED";
                    $under_attack->user_id = $saleRefuse->owner_id;
                    $under_attack->percentage_card_refused = $refusedPercentage;
                    $under_attack->start_date_card_refused = $start_date;
                    $under_attack->end_date_card_refused = $end_date;
                    $under_attack->total_refused = $saleRefuse->totalSales . "/" . $saleRefuse->refusedCount;
                    $under_attack->save();
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
