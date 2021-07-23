<?php

namespace Modules\Core\Listeners;

use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Events\NewChargebackEvent;
use Modules\Core\Services\EmailService;

class SendChargebackNotificationsListener
{
    public function __construct()
    {
        //
    }

    /**
     * @throws PresenterException
     */
    public function handle(NewChargebackEvent $event)
    {
        $sale = $event->sale;
        $salePresenter = $sale->present();

        $data = [
            'transaction' => hashids_encode($event->sale->id, 'sale_id'),
            'products' => $salePresenter->getProducts(),
            'subtotal' => $salePresenter->getFormattedSubTotal(),
            'shipment_value' => $salePresenter->getFormattedShipmentValue(),
            'discount' => $salePresenter->getFormattedDiscount(),
            'project_contact' => 'help@cloudfox.net',
            'total_value' => $salePresenter->getTotalPaidValueWithoutInstallmentTax(),
        ];

        (new EmailService())->sendEmailChargeback($data, $event->sale->user);
    }
}
