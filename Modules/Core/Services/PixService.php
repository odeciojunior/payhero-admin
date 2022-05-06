<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Transaction;
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

            $sales = Sale::with('transactions')
            ->where('payment_method', '=', Sale::PIX_PAYMENT)
            ->where('status', Sale::STATUS_PENDING)
            ->whereHas(
                'pixCharges',
                function ($querySale) {
                    $querySale->whereIn('status', ['ATIVA']);
                    $querySale->where( 'created_at', '<=', Carbon::now()->subHour()->toDateTimeString());
                }
            );

            foreach ($sales->cursor() as $sale) {

                $sale->update(['status' => Sale::STATUS_CANCELED]);

                foreach ($sale->transactions as $transaction) {
                    $transaction->update([
                            'status' => 'canceled',
                            'status_enum' => Transaction::STATUS_CANCELED,
                    ]);
                }

                SaleLog::create(
                    [
                        'status' => 'canceled',
                        'status_enum' => 5,
                        'sale_id' => $sale->id,
                    ]
                );

                $pix = $sale->pixCharges->where('status', 'ATIVA')->first();

                if (!FoxUtils::isEmpty($pix)) {
                    //Atualizar pixCharges
                    $pix->update(['status' => 'EXPIRED']);
                }

                event(new PixExpiredEvent($sale));

            }
        } catch (Exception $e) {
            report($e);
        }
    }
}

