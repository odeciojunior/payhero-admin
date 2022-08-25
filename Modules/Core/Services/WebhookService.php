<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Webhook;
use Modules\Core\Entities\WebhookLog;

/**
 * Class WebhookService
 *
 * @package Modules\Core\Services
 */
class WebhookService
{
    private $webhook;

    /**
     * WebhookService constructor.
     *
     * @param Webhook $webhook
     * @return void
     */
    function __construct(Webhook $webhook)
    {
        $this->webhook = $webhook;
    }

    /**
     * Formats the sale status update data.
     *
     * @param $sale
     * @return void
     */
    function saleStatusUpdate($sale)
    {
        try {
            $data = [
                "transaction_id" => hashids_encode($sale->id, "sale_id"),
                "status" => $sale->present()->getStatus(),
                "updated_at" => $sale->updated_at->format("Y-m-d H:i:s"),
            ];

            self::sendPost($data);
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * Formats the tracking code status update data.
     *
     * @param $tracking
     * @return void
     */
    function trackingCodeStatusUpdate($tracking)
    {
        try {
            $data = [
                "tracking_id" => hashids_encode($tracking->id),
                "tracking_status" => $tracking
                    ->present()
                    ->getTrackingStatusEnum($tracking->tracking_status_enum),
                "system_status" => $tracking
                    ->present()
                    ->getSystemStatusEnum($tracking->system_status_enum),
                "updated_at" => $tracking->updated_at->format("Y-m-d H:i:s"),
            ];

            self::sendPost($data);
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * Send a request to the URL configured in the webhook.
     *
     * @param $data
     * @return void
     */
    private function sendPost($data)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $this->webhook->url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            $this->storeWebhookLogs($data, $response);
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * Stores webhook send and return data.
     *
     * @param $data
     * @param $response
     * @return void
     */
    private function storeWebhookLogs($data, $response)
    {
        try {
            WebhookLog::create([
                "user_id" => $this->webhook->user_id,
                "company_id" => $this->webhook->company_id,
                "url" => $this->webhook->url,
                "sent_data" => json_encode($data),
                "response" => !json_decode($response)
                    ? json_encode($response)
                    : $response,
            ]);
        } catch (Exception $e) {
            report($e);
        }
    }
}
