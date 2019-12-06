<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Product;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use Modules\Core\Entities\Whatsapp2Sent;

class Whatsapp2Service
{
    /**
     * @var
     */
    public $urlCheckout;
    /**
     * @var
     */
    public $urlOrder;
    /**
     * @var
     */
    public $apiToken;
    /**
     * @var
     */
    private $integrationId;

    /**
     * DigitalManagerService constructor.
     * @param $urlCheckout
     * @param $urlOrder
     * @param $apiToken
     * @param $integrationId
     */
    function __construct($urlCheckout, $urlOrder, $apiToken, $integrationId)
    {
        $this->urlCheckout   = $urlCheckout;
        $this->urlOrder      = $urlOrder;
        $this->apiToken      = $apiToken;
        $this->integrationId = $integrationId;
    }

    /**
     * @param $data
     * @return bool|string
     */
    private function sendPost($data, $url)
    {
        $data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        if (!empty($data))
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $headers   = ['Content-Type: application/json','Content-Length: ' . strlen($data)];
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
     * @param  $sale
     * @param  $planSales
     * @param  $domain
     * @param  $eventSale
     */
    public function sendSale($sale, $planSales, $domain, $eventSale)
    {
        try {

            $dataProducts = [];
            foreach ($planSales as $planSale) {
                $dataProducts[] = [
                    'id'    => $planSale->plan_id,
                    'name'  => $planSale->plan->name,
                    'price' => $planSale->plan->price,
                    'qty'   => $planSale->amount,
                    'image' => '',
                ];
            }

            $status = ($eventSale == 2 || $eventSale == 3) ? 'paid' : 'pending';

            $data = [
                'type'      => 'order',
                'api_token' => $this->apiToken,
                'order'     => [
                    'token'            => Hashids::encode($sale->checkout_id),
                    'financial_status' => $status,
                    'billet_url'       => $sale->boleto_link,
                    'gateway'          => 'cloudfox',
                    'checkout_url'     => "https://checkout." . $domain->name . "/recovery/" . $sale->checkout->id_log_session,
                    'id'               => $sale->id,
                    'status'           => $status,
                    'values'           => [
                        'total' => $sale->total_paid_value,
                    ],
                    'costumer'         => [
                        'name'             => $sale->client->name,
                        'email'            => $sale->client->email,
                        'doc'              => $sale->client->document,
                        'phone_number'     => preg_replace( '/[^0-9]/', '', $sale->client->telephone),
                        'address'          => $sale->delivery->street,
                        'address_number'   => $sale->delivery->number,
                        'address_comp'     => $sale->delivery->complement,
                        'address_district' => $sale->delivery->neighborhood,
                        'address_city'     => $sale->delivery->city,
                        'address_state'    => $sale->delivery->state,
                        'address_country'  => $sale->delivery->country,
                        'address_zip_code' => preg_replace( '/[^0-9]/', '', $sale->delivery->zip_code),
                    ],
                    'products' => $dataProducts,
                ],
                'created_at' => $sale->start_date,
                'updated_at' => Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->toDateTimeString()
            ];

            $return = $this->sendPost($data, $this->urlOrder);
            if(isset($return['code']) && $return['code'] == 200) {
                $sentStatus = 2;
            } else {
                $sentStatus = 1;
            }
            Whatsapp2Sent::create(
                [
                    'data'                     => json_encode($data),
                    'response'                 => json_encode($return),
                    'sent_status'              => $sentStatus,
                    'instance_id'              => $sale->id,
                    'instance'                 => 'sale',
                    'event_sale'               => $eventSale,
                    'whatsapp2_integration_id' => $this->integrationId,
                ]
            );
            return $return;
        } catch (Exception $e) {
            report($e);
        }
    }
}