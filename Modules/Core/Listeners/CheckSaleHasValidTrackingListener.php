<?php


namespace Modules\Core\Listeners;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Events\CheckSaleHasValidTrackingEvent;

class CheckSaleHasValidTrackingListener implements ShouldQueue
{
    use Queueable;

    /**
     * @param  CheckSaleHasValidTrackingEvent  $event
     * @throws PresenterException
     */
    public function handle(CheckSaleHasValidTrackingEvent $event)
    {
        $salesModel = new Sale();
        $trackingPresenter = (new Tracking())->present();
        $productPresenter = (new Product())->present();

        $sale = $salesModel->with([
            'productsPlansSale.tracking',
            'productsPlansSale.product',
            'transactions'
        ])->find($event->saleId);

        if(!empty($sale)) {
            $hasInvalidOrNotInformedTracking = false;

            foreach ($sale->productsPlansSale as $pps) {
                if ($pps->product->type_enum == $productPresenter->getType('physical')) {
                    $hasInvalidOrNotInformedTracking = is_null($pps->tracking) || !in_array($pps->tracking->system_status_enum,
                            [
                                $trackingPresenter->getSystemStatusEnum('valid'),
                                $trackingPresenter->getSystemStatusEnum('checked_manually'),
                            ]);
                    if ($hasInvalidOrNotInformedTracking) {
                        break;
                    }
                }
            }

            if (!$hasInvalidOrNotInformedTracking) {
                $sale->has_valid_tracking = true;
                $sale->save();
            }
        }
    }
}
