<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\PixCharge;
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
    public function changePixPending()
    {
        try {

            $sales = Sale::where(
                [
                    ['payment_method', '=', Sale::PIX_PAYMENT],
                    ['status', '=', Sale::STATUS_PENDING]
                ]
            )
            ->whereHas(
                'pixCharges',
                function ($querySale) {
                    $querySale->where('created_at', '<=',
                            Carbon::now()->subHour()->toDateTimeString()
                    );
                }
            )
            ->get();

            foreach ($sales as $sale) {
//                $sale->update(['status' => Sale::STATUS_CANCELED]);
//                $pix = $sale->pixCharges->where('status', 'ATIVA')->first();

//                if (!FoxUtils::isEmpty($pix)) {
//                    $pix->update(['status' => 'EXPIRED']);
//                }
                event(new PixExpiredEvent($sale));
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}

