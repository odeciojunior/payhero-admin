<?php

declare(strict_types=1);

namespace Modules\Webhooks\Listeners;

use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Webhook;
use Modules\Webhooks\Services\WebhookService;
use Throwable;

class WebhookSaleListener
{
    public function handle($event): void
    {
        try {
            if (!is_null($event->sale->project_id)) {
                $checkoutConfig = CheckoutConfig::query()
                    ->where("project_id", $event->sale->project_id)
                    ->first();
                $companyId = $checkoutConfig->company_id ?? null;
            } else {
                $transaction = $event->sale->transactions
                    ->where("type", Transaction::TYPE_PRODUCER)
                    ->where("user_id", $event->sale->owner_id)
                    ->first();
                $companyId = $transaction->company_id ?? null;
            }

            if (empty($companyId)) {
                return;
            }

            $webhook = Webhook::query()
                ->where("company_id", $companyId)
                ->first();

            if (!empty($webhook)) {
                $service = new WebhookService($webhook);
                $service->saleStatusUpdate($event->sale);

                return;
            }

            $saleInformation = $event->sale->saleInformation;

            if (!empty($saleInformation) && $saleInformation->return_url) {
                $webhook = new Webhook([
                    "user_id" => $event->sale->owner_id,
                    "company_id" => $companyId,
                    "url" => $saleInformation->return_url,
                ]);
                $service = new WebhookService($webhook);
                $service->saleStatusUpdate($event->sale);
            }
        } catch (Throwable $th) {
            report($th);
        }
    }
}
