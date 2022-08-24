<?php

namespace Modules\Core\Services;

use Exception;

/**
 * Class WebhookService
 * @package Modules\Core\Services
 */
class WebhookService
{
    private $link;

    /**
     * WebhookService constructor.
     * @param $link
     */
    function __construct($link)
    {
        $this->link = $link;
    }

    /**
     * @param $sale
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
     * @param $data
     */
    private function sendPost($data)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $this->link,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);
        } catch (Exception $e) {
            report($e);
        }
    }
}
