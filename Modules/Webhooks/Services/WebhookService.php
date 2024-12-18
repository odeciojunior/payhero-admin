<?php

declare(strict_types=1);

namespace Modules\Webhooks\Services;

use Exception;
use Modules\Core\Entities\Webhook;
use Modules\Core\Entities\WebhookLog;

class WebhookService
{
    private float $startResponseTime = 0;
    private float $endResponseTime = 0;

    public function __construct(
        private readonly Webhook $webhook
    ) {
    }

    public function saleStatusUpdate($sale): void
    {
        try {
            $data = [
                'event_type' => 'sale.updated',
                'transaction_id' => hashids_encode($sale->id, 'sale_id'),
                'status' => $sale->present()->getStatus(),
                'updated_at' => $sale->updated_at->format('Y-m-d H:i:s'),
            ];

            $this->sendPost($data, $sale->id);
        } catch (Exception $e) {
            report($e);
        }
    }

    public function trackingCodeStatusUpdate($tracking): void
    {
        try {
            $data = [
                'event_type' => 'tracking.updated',
                'tracking_id' => hashids_encode($tracking->id),
                'tracking_status' => $tracking->present()->getTrackingStatusEnum($tracking->tracking_status_enum),
                'system_status' => $tracking->present()->getSystemStatusEnum($tracking->system_status_enum),
                'updated_at' => $tracking->updated_at->format('Y-m-d H:i:s'),
            ];

            $this->sendPost($data);
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * Send a request to the URL configured in the webhook.
     *
     * @param $data
     * @param $saleId
     * @return void
     */
    private function sendPost($data, $saleId = null): void
    {
        try {
            $this->startResponseTime = microtime(true);
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $this->webhook->url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ]);

            $response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            $this->endResponseTime = microtime(true);

            $this->storeWebhookLogs($response, $status, $data, $saleId);
        } catch (Exception $e) {
            report($e);
        }
    }

    private function storeWebhookLogs($response, $status, $data, $saleId = null): void
    {
        try {
            WebhookLog::create([
                'webhook_id' => $this->webhook->id,
                'user_id' => $this->webhook->user_id,
                'company_id' => $this->webhook->company_id,
                'sale_id' => $saleId,
                'url' => $this->webhook->url,
                'sent_data' => json_encode($data),
                'response' => !json_decode($response) ? json_encode($response) : $response,
                'response_status' => $status,
                'response_time' => ($this->endResponseTime - $this->startResponseTime) * 1000,
            ]);
        } catch (Exception $e) {
            report($e);
        }
    }
}
