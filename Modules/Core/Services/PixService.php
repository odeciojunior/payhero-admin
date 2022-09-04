<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Events\PixExpiredEvent;

/**
 * Class PixService
 * @package Modules\Core\Services
 */
class PixService
{
    /**
     *  Pix Pending
     * @return void
     */
    public function changePixToCanceled()
    {
        try {
            $sales = Sale::with("transactions")
                ->where("payment_method", "=", Sale::PIX_PAYMENT)
                ->where("status", Sale::STATUS_PENDING)
                ->whereHas("pixCharges", function ($querySale) {
                    $querySale->where("status", "ATIVA");
                    $querySale->where(
                        "created_at",
                        "<=",
                        Carbon::now()
                            ->subHour()
                            ->toDateTimeString()
                    );
                });

            foreach ($sales->cursor() as $sale) {
                $sale->update(["status" => Sale::STATUS_CANCELED]);

                foreach ($sale->transactions as $transaction) {
                    $transaction->update([
                        "status" => "canceled",
                        "status_enum" => Transaction::STATUS_CANCELED,
                    ]);
                }

                SaleService::createSaleLog($sale->id, "canceled");

                $pix = $sale->pixCharges->where("status", "ATIVA")->first();

                if (!FoxUtils::isEmpty($pix)) {
                    //Atualizar pixCharges
                    $pix->update(["status" => "EXPIRED"]);
                }

                if (!$sale->api_flag && $sale->owner_id > User::DEMO_ID) {
                    event(new PixExpiredEvent($sale));
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
