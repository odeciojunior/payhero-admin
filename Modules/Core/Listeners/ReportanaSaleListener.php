<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\ReportanaService;

class ReportanaSaleListener implements ShouldQueue
{
    public string $queue = "default";

    public function handle($event): void
    {
        try {
            $sale = $event->sale;
            if ($sale->api_flag) {
                return;
            }

            $isShopify = ShopifyIntegration::where("project_id", $sale->project_id)
                ->where("status", ShopifyIntegration::STATUS_APPROVED)
                ->exists();

            //if it's shopify, wait until the sale has the order_id
            if (
                !$isShopify ||
                $sale->shopify_order ||
                in_array($sale->status, [Sale::STATUS_REFUSED, Sale::STATUS_CANCELED_ANTIFRAUD])
            ) {
                if ($sale->payment_method == Sale::CREDIT_CARD_PAYMENT) {
                    if ($sale->status == Sale::STATUS_APPROVED) {
                        $column = $eventName = "credit_card_paid";
                    }
                    if (in_array($sale->status, [Sale::STATUS_REFUSED, Sale::STATUS_CANCELED_ANTIFRAUD])) {
                        $column = $eventName = "credit_card_refused";
                    }
                } elseif ($sale->payment_method == Sale::BILLET_PAYMENT) {
                    if ($sale->status == Sale::STATUS_APPROVED) {
                        $column = $eventName = "billet_paid";
                    }
                    if ($sale->status == Sale::STATUS_PENDING) {
                        $column = "billet_generated";
                        $eventName = "billet_pending";
                    }
                    if ($sale->status == Sale::STATUS_CANCELED) {
                        $column = $eventName = "billet_expired";
                    }
                } elseif ($sale->payment_method == Sale::PIX_PAYMENT) {
                    if ($sale->status == Sale::STATUS_APPROVED) {
                        $column = $eventName = "pix_paid";
                    }
                    if ($sale->status == Sale::STATUS_PENDING) {
                        $column = "pix_generated";
                        $eventName = "pix_pending";
                    }
                    if ($sale->status == Sale::STATUS_CANCELED) {
                        $column = $eventName = "pix_expired";
                    }
                }

                if (isset($column, $eventName)) {
                    $integrations = ReportanaIntegration::where("project_id", $sale->project_id)
                        ->where($column, 1)
                        ->get();
                    $trackingCreatedEvent = !empty($event->trackingCreatedEvent) ? $event->trackingCreatedEvent : false;

                    foreach ($integrations as $integration) {
                        $reportanaService = new ReportanaService(
                            $integration->client_id,
                            $integration->client_secret,
                            $integration->id
                        );

                        $sale->load(["customer", "delivery", "plansSales.plan", "trackings"]);
                        $domain = Domain::where("status", 3)
                            ->where("project_id", $sale->project_id)
                            ->first();

                        $reportanaService->sendSale(
                            $sale,
                            $sale->plansSales,
                            $domain,
                            $eventName,
                            $trackingCreatedEvent
                        );
                    }
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
