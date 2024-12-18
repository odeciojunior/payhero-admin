<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Events\ManualRefundEvent;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\SendgridService;

/**
 * Class ManualRefundedSendEmailListener
 * @package Modules\Core\Listeners
 */
class ManualRefundedSendEmailListener implements ShouldQueue
{
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(ManualRefundEvent $event): void
    {
        try {
            $sale = $event->sale;
            if ($sale->api_flag || $sale->api) {
                return;
            }

            $emailService = new SendgridService();
            $saleService = new SaleService();
            $domainPresent = (new Domain())->present();

            $project = Project::find($sale->project_id);
            $domain = Domain::where("project_id", $project->id)
                ->where("status", $domainPresent->getStatus("approved"))
                ->first();

            $sale->setRelation("customer", $event->sale->customer);
            $customer = $sale->customer;
            if (stristr($customer->email, "invalido") !== false) {
                return;
            }

            $saleCode = hashids_encode($sale->id, "sale_id");
            $products = $saleService->getEmailProducts($sale->id);

            $sale->total_paid_value = preg_replace("/[^0-9]/", "", $sale->total_paid_value);
            $sale->total_paid_value = substr_replace(
                $sale->total_paid_value,
                ",",
                strlen($sale->total_paid_value) - 2,
                0
            );
            $sale->shipment_value = preg_replace("/[^0-9]/", "", $sale->shipment_value);
            $sale->shipment_value = substr_replace($sale->shipment_value, ",", strlen($sale->shipment_value) - 2, 0);

            $subTotal = preg_replace("/[^0-9]/", "", $sale->sub_total);
            $subTotal = substr_replace($subTotal, ",", strlen($subTotal) - 2, 0);

            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            if ($discount == 0 || $discount == null) {
                $discount = "";
            }
            if ($discount != "") {
                $discount = substr_replace($discount, ",", strlen($discount) - 2, 0);
            }

            $data = [
                "first_name" => $customer->present()->getFirstName(),
                "store_logo" => $project->checkoutConfig->checkout_logo,
                "project_name" => $project->name,
                "sale_code" => $saleCode,
                "products" => $products,
                "total_paid_value" => $sale->total_paid_value,
                "shipment_value" => $sale->shipment_value,
                "subtotal" => strval($subTotal),
                "discount" => $discount,
            ];

            $fromEmail = "noreply@" . ($domain ? $domain->name : "azcend.com.br");
            $emailService->sendEmail(
                $fromEmail,
                $project["name"],
                $customer["email"],
                $customer["name"],
                "d-29b4407a5fa04b179a2bec75da8796ea", /// done
                $data
            );
        } catch (Exception $e) {
            report($e);
        }
    }
}
