<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Sale;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use Modules\Core\Entities\SmartfunnelSent;

/**
 * Class SmartfunnelService
 * @package Modules\Core\Services
 */
class SmartfunnelService
{
    /**
     * @var
     */
    public $urlApi;
    /**
     * @var
     */
    private $integrationId;

    /**
     * SmartfunnelService constructor.
     * @param $urlApi
     * @param $integrationId
     */
    function __construct($urlApi, $integrationId)
    {
        $this->urlApi        = $urlApi;
        $this->integrationId = $integrationId;
    }

    /**
     * @param $data
     * @param $url
     * @return array
     */
    private function sendPost($data)
    {
        $data = json_encode($data);
        $ch   = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        if (!empty($data))
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $headers = ['Content-Type: application/json', 'Content-Length: ' . strlen($data)];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            return ['code' => $httpCode, 'result' => curl_error($ch)];
        }
        curl_close($ch);

        return ['code' => $httpCode, 'result' => json_decode($result, true)];
    }

    /**
     * @param $sale
     * @param $planSales
     * @param $domain
     * @param $eventSale
     * @return array
     */
    public function sendSale($sale, $planSales, $domain, $eventSale)
    {
        try {
            if (!empty($domain)) {

                $dataProducts = [];
                foreach ($planSales as $planSale) {
                    $dataProducts[] = [
                        'id'       => Hashids::encode($planSale->plan_id),
                        'name'     => $planSale->plan->name,
                        'price'    => $planSale->plan->price,
                        'quantity' => $planSale->amount,
                    ];
                }

                $totalValue = number_format(($sale->sub_total + $sale->shipment_value), 2, '.', '');

                $data = [
                    'event_type'       => $eventSale,
                    'utm_campaign'     => $sale->checkout->utm_campaign,
                    'sale_id'          => Hashids::connection('sale_id')->encode($sale->id),
                    'total_value'      => $totalValue,
                    'checkout_url'     => "https://checkout." . $domain->name . "/recovery/" . Hashids::encode($sale->checkout_id),
                    'billet_url'       => $sale->boleto_link,
                    "billet_bar_code"  => $sale->boleto_digitable_line,
                    'customer'         => [
                        'name'          => $sale->customer->name,
                        'email'         => $sale->customer->present()->getEmail(),
                        'document'      => $sale->customer->document,
                        'phone_number'  => preg_replace('/[^0-9]/', '', $sale->customer->telephone),
                        'street'        => $sale->delivery->street,
                        'street_number' => $sale->delivery->number,
                        'complement'    => $sale->delivery->complement,
                        'district'      => $sale->delivery->neighborhood,
                        'city'          => $sale->delivery->city,
                        'state'         => $sale->delivery->state,
                        'country'       => $sale->delivery->country,
                        'zip_code'      => preg_replace('/[^0-9]/', '', $sale->delivery->zip_code),
                    ],
                    'products'   => $dataProducts,
                    'created_at' => $sale->start_date,
                ];

                $return = $this->sendPost($data);
                if (isset($return['code']) && $return['code'] > 199 && $return['code'] < 300) {
                    $sentStatus = (new SmartfunnelSent())->present()->getSentStatus('success');
                } else {
                    $sentStatus = (new SmartfunnelSent())->present()->getSentStatus('error');
                }
                SmartfunnelSent::create(
                    [
                        'data'                       => json_encode($data),
                        'response'                   => json_encode($return),
                        'sent_status'                => $sentStatus,
                        'sale_id'                    => $sale->id,
                        'event_sale'                 => (new SmartfunnelSent())->present()->getEvent($eventSale),
                        'smartfunnel_integration_id' => $this->integrationId,
                    ]
                );

                return $return;
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
