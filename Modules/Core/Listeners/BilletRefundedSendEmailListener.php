<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Events\BilletRefundedEvent;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class BilletRefundedSendEmailListener
 * @package Modules\Core\Listeners
 */
class BilletRefundedSendEmailListener implements ShouldQueue
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
     * @param BilletRefundedEvent $event
     * @return bool
     */
    public function handle(BilletRefundedEvent $event)
    {
        try {
            $emailService = new SendgridService();
            $saleService = new SaleService();
            $projectModel = new Project();
            $domainModel = new Domain();
            $domainPresent = $domainModel->present();

            $sale = $event->sale;
            $project = $projectModel->find($sale->project_id);
            $domain = $domainModel->where('project_id', $project->id)
                ->where('status', $domainPresent->getStatus('approved'))->first();

            if (empty($domain)) {
                return false;
            }

            $sale->setRelation('customer', $event->sale->customer);
            $customer = $sale->customer;
            if (stristr($customer->email, 'invalido') !== false) {
                return false;
            }

            $saleCode = Hashids::connection('sale_id')->encode($sale->id);
            $products = $saleService->getEmailProducts($sale->id);

            $sale->total_paid_value = preg_replace(
                "/[^0-9]/",
                "",
                $sale->total_paid_value
            );
            $sale->total_paid_value = substr_replace(
                $sale->total_paid_value,
                ',',
                strlen($sale->total_paid_value) - 2,
                0
            );
            $sale->shipment_value = preg_replace("/[^0-9]/", "", $sale->shipment_value);
            $sale->shipment_value = substr_replace($sale->shipment_value, ',', strlen($sale->shipment_value) - 2, 0);

            $subTotal = preg_replace("/[^0-9]/", "", $sale->sub_total);
            $subTotal = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);

            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            if ($discount == 0 || $discount == null) {
                $discount = '';
            }
            if ($discount != '') {
                $discount = substr_replace($discount, ',', strlen($discount) - 2, 0);
            }

            $data = [
                'first_name' => $customer->present()->getFirstName(),
                "store_logo" => $project->logo,
                "project_contact" => $project->contact,
                "project_name" => $project->name,
                'sale_code' => $saleCode,
                "products" => $products,
                "total_paid_value" => $sale->total_paid_value,
                "shipment_value" => $sale->shipment_value,
                "subtotal" => strval($subTotal),
                'discount' => $discount,
                "sac_link" => "https://sac." . $domain->name,
            ];

            $emailService->sendEmail(
                'noreply@' . $domain['name'],
                $project['name'],
                $customer['email'],
                $customer['name'],
                'd-8c39acea2c2c4d94978dc16f5d518c05',
                $data
            );
        } catch (Exception $e) {
            report($e);
        }
    }
}
