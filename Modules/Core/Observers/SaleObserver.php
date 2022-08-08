<?php

namespace Modules\Core\Observers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;

class SaleObserver
{
    public function updating(Sale $sale)
    {
        try {
            if ($sale->getOriginal("has_valid_tracking") != $sale->has_valid_tracking) {
                if (!$sale->relationLoaded("transactions")) {
                    $sale->load("transactions");
                }

                Redis::connection("redis-statement")->set(
                    "sale:has:tracking:{$sale->id}",
                    $sale->getValidTrackingForRedis()
                );

                foreach ($sale->transactions as $transaction) {
                    if (
                        $transaction->release_date <= Carbon::now()->format("Y-m-d") &&
                        $transaction->status_enum == Transaction::STATUS_PAID &&
                        $transaction->gateway_id == Gateway::GETNET_PRODUCTION_ID
                    ) {
                        $transaction->update([
                            "is_waiting_withdrawal" => 1,
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
