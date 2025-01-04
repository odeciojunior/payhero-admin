<?php

declare(strict_types=1);

namespace Modules\Webhooks\Listeners;

use Illuminate\Support\Facades\Log;
use JsonException;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Webhook;
use Modules\Webhooks\Actions\GenerateSignatureWebhookAction;
use Modules\Webhooks\Services\WebhookService;
use Throwable;

class WebhookSaleListener
{
    public function handle($event): void
    {
        try {
            $sale = $event->sale;
            if ($sale->project_id) {
                /**
                 * @var CheckoutConfig $checkoutConfig
                 */
                $checkoutConfig = CheckoutConfig::query()
                    ->where("project_id", $sale->project_id)
                    ->first();
                $companyId = $checkoutConfig?->company_id;
            } else {
                $transaction = $sale->transactions
                    ->where('type', Transaction::TYPE_PRODUCER)
                    ->where('user_id', $sale->owner_id)
                    ->first();
                $companyId = $transaction->company_id ?? null;
            }

            if (is_null($companyId)) {
                Log::info('webhook company id not found', [
                    'sale' => $sale->id,
                ]);
                return;
            }

            $this->createWebhookBySalePostbackUrl($sale, $companyId);

            $webhooks = Webhook::query()
                ->where('company_id', $companyId)
                ->get();

            foreach ($webhooks as $webhook) {
                $service = new WebhookService($webhook);
                $service->saleStatusUpdate($sale);
            }
        } catch (Throwable $th) {
            report($th);
        }
    }

    /**
     * @throws JsonException
     */
    private function createWebhookBySalePostbackUrl(Sale $sale, int $companyId): void
    {
        $saleInformation = $sale->saleInformation;

        if (empty($saleInformation) || empty($saleInformation->return_url)) {
            return;
        }

        /**
         * @var Webhook $webhookApiPayment
         */
        $webhookApiPayment = Webhook::query()
            ->firstOrCreate([
                'user_id' => $sale->owner_id,
                'company_id' => $companyId,
                'url' => $saleInformation->return_url,
                'description' => 'api_postback_url',
            ], [
                'signature' => GenerateSignatureWebhookAction::handle([
                    'user_id' => $sale->owner_id,
                    'company_id' => $companyId,
                    'description' => 'api_postback_url',
                    'url' => $saleInformation->return_url,
                ])
            ]);

        if (is_null($webhookApiPayment->signature)) {
            $webhookApiPayment->update([
                'signature' => GenerateSignatureWebhookAction::handle([
                    'user_id' => $sale->owner_id,
                    'company_id' => $companyId,
                    'description' => 'api_postback_url',
                    'url' => $saleInformation->return_url,
                ]),
            ]);
        }
    }
}
