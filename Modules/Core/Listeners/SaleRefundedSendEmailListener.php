<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Events\SaleRefundedEvent;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

class SaleRefundedSendEmailListener implements ShouldQueue
{
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param SaleRefundedEvent $event
     */
    public function handle(SaleRefundedEvent $event)
    {
        try {
            $emailService = new SendgridService();
            $saleService  = new SaleService();
            $projectModel = new Project();
            $domainModel  = new Domain();

            $sale = $event->sale;
            $sale->setRelation('customer', $event->sale->customer);

            $customer = $sale->customer;
            $saleCode = Hashids::connection('sale_id')->encode($sale->id);
            $project  = $projectModel->find($sale->project_id);
            $domain   = $domainModel->where('project_id', $project->id)->where('status', 3)->first();
            $products = $saleService->getEmailProducts($sale->id);

            $subTotal               = preg_replace("/[^0-9]/", "", $sale->sub_total);
            $iof                    = preg_replace("/[^0-9]/", "", $sale->iof);
            $discount               = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            $sale->total_paid_value = preg_replace("/[^0-9]/", "", $sale->iof) + preg_replace("/[^0-9]/", "",
                                                                                              $sale->total_paid_value);
            $sale->total_paid_value = substr_replace($sale->total_paid_value, ',', strlen($sale->total_paid_value) - 2,
                                                     0);
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
            if (!empty($domain->name)) {

                $data = [
                    'first_name'          => $customer->present()->getFirstName(),
                    "store_logo"          => $project->logo,
                    "project_contact"     => $project->contact,
                    'sale_code'           => $saleCode,
                    "products"            => $products,
                    "total_paid_value"    => $sale->total_paid_value,
                    "shipment_value"      => $sale->shipment_value,
                    "subtotal"            => strval($subTotal),
                    "iof"                 => $iof,
                    'discount'            => $discount,
                    'installments_amount' => $sale->installments_amount,
                    'installments_value'  => number_format($sale->installments_value, 2, ',', '.'),
                    "sac_link"            => "https://sac." . $domain->name,
                ];
                $emailService->sendEmail('noreply@' . $domain['name'], $project['name'], $customer['email'],
                                         $customer['name'], 'd-97d5002e189546778c0bedf4f4e540f1', $data);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao enviar email de boleto pago');
            report($e);
        }
    }
}
