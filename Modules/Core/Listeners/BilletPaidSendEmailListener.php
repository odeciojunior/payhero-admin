<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Events\BilletPaidEvent;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

class BilletPaidSendEmailListener
{
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * @param BilletPaidEvent $event
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function handle(BilletPaidEvent $event)
    {
        try {
            $sendGridService = new SendgridService();
            $saleService     = new SaleService();
            $projectModel    = new Project();
            $domainModel     = new Domain();
            $sale            = $event->sale;
            $client          = $event->client;
            $saleCode        = Hashids::connection('sale_id')->encode($sale->id);
            $project         = $projectModel->find($event->plan->project_id);
            $domain          = $domainModel->where('project_id', $project->id)->first();
            $products        = $saleService->getEmailProducts($sale->id);

            $subTotal               = preg_replace("/[^0-9]/", "", $sale->sub_total);
            $iof                    = preg_replace("/[^0-9]/", "", $sale->iof);
            $discount               = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            $sale->total_paid_value = preg_replace("/[^0-9]/", "", $sale->iof) + preg_replace("/[^0-9]/", "", $sale->total_paid_value);
            $sale->total_paid_value = substr_replace($sale->total_paid_value, ',', strlen($sale->total_paid_value) - 2, 0);
            $sale->shipment_value   = preg_replace("/[^0-9]/", "", $sale->shipment_value);
            $sale->shipment_value   = substr_replace($sale->shipment_value, ',', strlen($sale->shipment_value) - 2, 0);
            $subTotal               = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);

            if ($iof == 0) {
                $iof = '';
            } else {
                $iof = substr_replace($iof, ',', strlen($iof) - 2, 0);
            }
            if ($discount == 0 || $discount == null) {
                $discount = '';
            }
            if ($discount != '') {
                $discount = substr_replace($discount, ',', strlen($discount) - 2, 0);
            }

            $data = [
                'first_name'       => $client->present()->getFirstName(),
                "store_logo"       => $project->logo,
                "project_contact"  => $project->contact,
                'sale_code'        => $saleCode,
                "products"         => $products,
                "total_paid_value" => $sale->total_paid_value,
                "shipment_value"   => $sale->shipment_value,
                "subtotal"         => strval($subTotal),
                "iof"              => $iof,
                'discount'         => $discount,
            ];
            $sendGridService->sendEmail('noreply@' . $domain['name'], $project['name'], $client['email'], $client['name'], 'd-c1e4278e88dd417aa38a18fc03694718', $data);
        } catch (Exception $e) {
            Log::warning('Erro ao enviar email de boleto pago');
            report($e);
        }
    }
}
