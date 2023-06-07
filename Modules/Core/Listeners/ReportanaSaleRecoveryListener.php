<?php

namespace App\Listeners\Reportana;

use Exception;

use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Presenters\ReportanaIntegrationPresenter;
use Modules\Core\Services\ReportanaService;

class ReportanaSaleRecoveryListener implements ShouldQueue
{
    public $queue = "default";

    public function handle($event)
    {
        try {
            if (!empty($event->sale)) {
                $sale = $event->sale;

                if ($sale->api_flag) {
                    return;
                }

                $isShopify = ShopifyIntegration::where("project_id", $sale->project_id)->where("status", ShopifyIntegration::STATUS_APPROVED)->exists();

                if (!$isShopify || $sale->shopify_order || in_array($sale->status, [Sale::STATUS_REFUSED, Sale::STATUS_CANCELED_ANTIFRAUD]) || $sale->reportana_recovery_flag) {
                    $reportanaService = new ReportanaService("https://api.reportana.com/2022-05/orders", 0);

                    $sale->load(["customer", "delivery", "plansSales.plan", "trackings"]);

                    $domain = Domain::where("status", 3)->where("project_id", $sale->project_id)->first();

                    $eventName = ReportanaIntegrationPresenter::getSearchEvent($sale->payment_method);

                    $reportanaService->sendSaleApi($sale, $sale->plansSales, $domain, $eventName);
                }
            } else {
                $reportanaService = new ReportanaService("https://api.reportana.com/2022-05/abandoned-checkouts", 0);

                $checkout = $event->checkout;

                $checkout->load("checkoutPlans.plan");

                $reportanaService->sendAbandonedCartApi($checkout, $checkout->checkoutPlans, $event->domain, $event->log);
            }
        } catch (Exception $e) {
            Log::warning("Erro ao enviar notificação para Reportana na venda de recuperação " . $event->sale->id);

            report($e);
        }
    }
}
