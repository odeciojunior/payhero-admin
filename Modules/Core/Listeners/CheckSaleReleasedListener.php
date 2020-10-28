<?php


namespace Modules\Core\Listeners;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Events\CheckSaleReleasedEvent;
use Modules\Core\Services\CheckoutService;

class CheckSaleReleasedListener implements ShouldQueue
{
    use Queueable;

    /**
     * @param  CheckSaleReleasedEvent  $event
     * @throws PresenterException
     */
    public function handle(CheckSaleReleasedEvent $event)
    {
        $salesModel = new Sale();
        $trackingPresenter = (new Tracking())->present();
        $productPresenter = (new Product())->present();
        $checkoutService = new CheckoutService();

        $sale = $salesModel->with([
            'productsPlansSale.tracking',
            'productsPlansSale.product',
            'transactions'
        ])->whereIn('gateway_id', [14, 15])//getnet
            ->find($event->saleId);

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

                if(!$sale->transactions->whereNotNull('gateway_released_at')->count()){
                    $checkoutService->releasePaymentGetnet($sale->id);

                    //$result = $checkoutService->releasePaymentGetnet($sale->id);
                    //if (!empty($result) && $result->status == 'success') {
                    ////   faz alguma coisa com o resultado
                    //}
                }
            }
        }
    }
}