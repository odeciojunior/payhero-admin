<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\PixCharge;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\PixExpiredEvent;
use Vinkla\Hashids\Facades\Hashids;

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

            $sales = Sale::where('payment_method', '=', Sale::PIX_PAYMENT)
                            ->where('status', Sale::STATUS_PENDING)
                            ->where( 'created_at', '<=', Carbon::now()->subHour()->toDateTimeString());

            foreach ($sales->cursor() as $sale) {

                $sale->update(['status' => Sale::STATUS_CANCELED]);

                foreach ($sale->transactions as $transaction) {
                    $transaction->update([
                            'status' => 'canceled',
                            'status_enum' => Transaction::STATUS_CANCELED,
                    ]);
                }

                SaleLog::create([
                        'status' => 'canceled',
                        'status_enum' => 5,
                        'sale_id' => $sale->id,
                ]);

                event(new PixExpiredEvent($sale));

            }
        } catch (Exception $e) {
            report($e);
        }
    }
}

