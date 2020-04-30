<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Sale;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use Modules\Core\Entities\ReportanaSent;
use Modules\Core\Entities\ReportanaIntegration;

/**
 * Class ReportanaService
 * @package Modules\Core\Services
 */
class ReportanaService
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
     * ReportanaService constructor.
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
                $checkout = $sale->checkout;

                $dataProducts = [];
                foreach ($planSales as $planSale) {
                    $dataProducts[] = [
                        'id'    => $planSale->plan_id,
                        'name'  => $planSale->plan->name,
                        'price' => $planSale->plan->price,
                        'qty'   => $planSale->amount,
                        'image' => $planSale->plan->products->first()->photo ?? '',
                    ];
                }

                $status = '';
                switch ($eventSale) {
                    case 'billet_pending':
                        $status = 'pending';
                        break;
                    case 'billet_paid':
                    case 'credit_card_paid':
                        $status = 'paid';
                        break;
                    case 'credit_card_refused':
                        $status = 'refused';
                        break;
                    case 'abandoned_cart':
                        $status = 'abandoned_cart';
                        break;
                }

                $totalValue = number_format(($sale->sub_total + $sale->shipment_value), 2, '.', '');
                $subtotal   = number_format($sale->sub_total, 2, '.', '');

                $data = [
                    'type'         => 'order',
                    'event_type'   => $eventSale,
                    'payment_type' => (new Sale())->present()->getPaymentType($sale->payment_method),
                    'order'        => [
                        'financial_status'  => $status,
                        'billet_url'        => $sale->boleto_link,
                        'gateway'           => 'cloudfox',
                        'checkout_url'      => "https://checkout." . $domain->name . "/recovery/" . Hashids::encode($sale->checkout_id),
                        'id'                => $sale->checkout_id,
                        'status'            => $status,
                        "codigo_barras"     => $sale->boleto_digitable_line,
                        "boleto_due_date"   => $sale->boleto_due_date,
                        "shopify_reference" => $sale->shopify_order,
                        'values'            => [
                            'subtotal' => $subtotal,
                            'total'    => $totalValue,
                        ],
                        'costumer'         => [
                            'name'             => $sale->customer->name,
                            'email'            => $sale->customer->email,
                            'doc'              => $sale->customer->document,
                            'phone_number'     => preg_replace('/[^0-9]/', '', $sale->customer->telephone),
                            'address'          => $sale->delivery->street,
                            'address_number'   => $sale->delivery->number,
                            'address_comp'     => $sale->delivery->complement,
                            'address_district' => $sale->delivery->neighborhood,
                            'address_city'     => $sale->delivery->city,
                            'address_state'    => $sale->delivery->state,
                            'address_country'  => $sale->delivery->country,
                            'address_zip_code' => preg_replace('/[^0-9]/', '', $sale->delivery->zip_code),
                        ],
                        'products'         => $dataProducts,
                    ],
                    'created_at'   => $sale->start_date,
                    'updated_at'   => Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->toDateTimeString(),
                ];

                $return = $this->sendPost($data);
                if (isset($return['code']) && $return['code'] > 199 && $return['code'] < 300) {
                    $sentStatus = 2;
                } else {
                    $sentStatus = 1;
                }
                ReportanaSent::create(
                    [
                        'data'                     => json_encode($data),
                        'response'                 => json_encode($return),
                        'sent_status'              => $sentStatus,
                        'instance_id'              => $sale->id,
                        'instance'                 => 'sale',
                        'event_sale'               => (new ReportanaIntegration())->present()->getEvent($eventSale),
                        'reportana_integration_id' => $this->integrationId,
                    ]
                );

                return $return;
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
