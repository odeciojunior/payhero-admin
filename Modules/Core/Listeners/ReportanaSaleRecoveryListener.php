<?php

namespace Modules\Core\Listeners;

use Exception;

use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\ReportanaIntegration;


use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Presenters\ReportanaIntegrationPresenter;
use Modules\Core\Services\ReportanaService;

class ReportanaSaleRecoveryListener implements ShouldQueue
{
    public string $queue = "default";

    public function handle($event): void
    {
        try {
            if (!empty($event->sale)) {
                $sale = $event->sale;

                if ($sale->api_flag) {
                    return;
                }

                $integration = ReportanaIntegration::where("project_id", $sale->project_id)->first();
                if ($integration === null) {
                    return;
                }

                $isShopify = ShopifyIntegration::where("project_id", $sale->project_id)->where("status", ShopifyIntegration::STATUS_APPROVED)->exists();

                if (!$isShopify || $sale->shopify_order || in_array($sale->status, [Sale::STATUS_REFUSED, Sale::STATUS_CANCELED_ANTIFRAUD, Sale::STATUS_CANCELED, Sale::STATUS_PENDING]) || $sale->reportana_recovery_flag) {
                    $reportanaService = new ReportanaService($integration->client_id, $integration->client_secret, $integration->id);

                    $sale->load(["customer", "delivery", "plansSales.plan", "trackings"]);

                    $domain = Domain::where("status", 3)->where("project_id", $sale->project_id)->first();

                    $eventName = ReportanaIntegrationPresenter::getSearchEvent($sale->payment_method, $sale->status);

                    $reportanaService->sendSale($sale, $sale->plansSales, $domain, $eventName);
                }
            } else {
                $checkout = $event->checkout;

                $checkout->load("checkoutPlans.plan");

                $integration = ReportanaIntegration::where("project_id", $checkout->project_id)->first();
                if ($integration === null) {
                    return;
                }

                $reportanaService = new ReportanaService($integration->client_id, $integration->client_secret, $integration->id);

                $reportanaService->sendAbandoned($checkout, $checkout->checkoutPlans, $event->domain, $event->log);
            }
        } catch (Exception $e) {
            Log::warning("Erro ao enviar notificação para Reportana na venda de recuperação " . $event->sale->id);

            report($e);
        }
    }
}
