<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Core\Entities\Whatsapp2Sent;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class Whatsapp2Service
 * @package Modules\Core\Services
 */
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

    function __construct($urlCheckout, $urlOrder, $apiToken, $integrationId)
    {
        $this->urlCheckout = $urlCheckout;
        $this->urlOrder = $urlOrder;
        $this->apiToken = $apiToken;
        $this->integrationId = $integrationId;
    }

    private function sendPost($data, $url): array
    {
        if (!FoxUtils::isProduction()) {
            return ['code' => 403, 'result' => "Funcionalidade habilitada somente em ambiente de produção!"];
        }

        $data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

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
                        'id' => $planSale->plan_id,
                        'name' => $planSale->plan->name,
                        'price' => $planSale->plan->price,
                        'qty' => $planSale->amount,
                        'image' => $planSale->plan->products->first()->photo ?? '',
                    ];
                }

                //$status = ($eventSale == 2 || $eventSale == 3) ? 'paid' : 'pending';

                //1. BoletoPendingEvent
                //2. BilletPaidEvent
                //3. CreditCardApprovedEvent
                //4. BilletExpiredEvent
                //5. CreditCardRefusedEvent
                //6. SaleRefundedEvent

                $status = '';
                switch ($eventSale) {
                    case 2:
                    case 3:
                        $status = 'paid';
                        break;
                    case 4:
                    case 6:
                        $status = 'order_cancelled';
                        break;
                    case 5:
                        $status = 'voided';
                        break;
                    default:
                        $status = 'pending';
                        break;
                }

                $totalValue = preg_replace("/[^0-9]/", "", $sale->sub_total) + preg_replace("/[^0-9]/", "",
                        $sale->shipment_value);
                $totalValue = substr_replace($totalValue, '.', strlen($totalValue) - 2, 0);

                $domainName = $domain->name??'cloudfox.net';
                $boletoLink = "https://checkout.{$domainName}/order/".Hashids::connection('sale_id')->encode($sale->id)."/download-boleto";

                $data = [
                    'type' => 'order',
                    'api_token' => $this->apiToken,
                    'payment_type' => (new Sale())->present()->getPaymentType($sale->payment_method),
                    'order' => [
                        'token' => Hashids::encode($sale->checkout_id),
                        'financial_status' => $status,
                        'billet_url' => $boletoLink,
                        'gateway' => 'cloudfox',
                        'checkout_url' => "https://checkout." . $domain->name . "/recovery/" . Hashids::encode($sale->checkout_id),
                        'id' => $sale->checkout_id,
                        'status' => $status,
                        "codigo_barras" => $sale->boleto_digitable_line,
                        'values' => [
                            'total' => $totalValue,
                        ],
                        'costumer' => [
                            'name' => $sale->customer->name,
                            'email' => $sale->customer->present()->getEmail(),
                            'doc' => $sale->customer->document,
                            'phone_number' => preg_replace('/[^0-9]/', '', $sale->customer->telephone),
                            'address' => $sale->delivery->street,
                            'address_number' => $sale->delivery->number,
                            'address_comp' => $sale->delivery->complement,
                            'address_district' => $sale->delivery->neighborhood,
                            'address_city' => $sale->delivery->city,
                            'address_state' => $sale->delivery->state,
                            'address_country' => $sale->delivery->country,
                            'address_zip_code' => preg_replace('/[^0-9]/', '', $sale->delivery->zip_code),
                        ],
                        'products' => $dataProducts,
                    ],
                    'created_at' => $sale->start_date,
                    'updated_at' => Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->toDateTimeString(),
                ];

                $return = $this->sendPost($data, $this->urlOrder);

                if (isset($return['code']) && $return['code'] == 200) {
                    $sentStatus = 2;
                } else {
                    $sentStatus = 1;
                }
                Whatsapp2Sent::create(
                    [
                        'data' => json_encode($data),
                        'response' => json_encode($return),
                        'sent_status' => $sentStatus,
                        'instance_id' => $sale->id,
                        'instance' => 'sale',
                        'event_sale' => $eventSale,
                        'whatsapp2_integration_id' => $this->integrationId,
                    ]
                );

                return $return;
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    public function sendPixSaleExpired($sale)
    {
        try {
            $sale->setRelation('customer', $sale->customer);
            $sale->load('plansSales', 'plansSales.plan', 'delivery', 'checkout');

            $planSales = $sale->plansSales;

            $dataProducts = [];
            foreach ($planSales as $planSale) {
                $dataProducts[] = [
                    'id' => $planSale->plan_id,
                    'name' => $planSale->plan->name,
                    'price' => $planSale->plan->price,
                    'qty' => $planSale->amount,
                    'image' => $planSale->plan->products->first()->photo ?? '',
                ];
            }

            $saleIdEncoded = hashids_encode($sale->id, 'sale_id');

            $domain = Domain::where('status', Domain::STATUS_APPROVED)
                ->where('project_id', $sale->project_id)
                ->first();
            $domainName = $domain->name ?? 'cloudfox.net';
            $link = "https://checkout.$domainName/pix/$saleIdEncoded";

            $totalValue = foxutils()->onlyNumbers($sale->sub_total) + foxutils()->onlyNumbers($sale->shipment_value);
            $totalValue = substr_replace($totalValue, '.', strlen($totalValue) - 2, 0);

            $data = [
                'type' => 'order',
                'api_token' => $this->apiToken,
                'payment_type' => 'pix',
                'order' => [
                    'token' => hashids_encode($sale->checkout_id),
                    'financial_status' => Whatsapp2Integration::STATUS_CANCELLED,
                    'gateway' => 'cloudfox',
                    'billet_url' => $link,
                    'id' => $sale->checkout_id,
                    'values' => ['total' => $totalValue],
                    'costumer' => [
                        'name' => $sale->customer->name,
                        'email' => $sale->customer->present()->getEmail(),
                        'doc' => $sale->customer->document,
                        'phone_number' => foxutils()->onlyNumbers($sale->customer->telephone),
                        'address' => $sale->delivery->street,
                        'address_number' => $sale->delivery->number,
                        'address_comp' => $sale->delivery->complement,
                        'address_district' => $sale->delivery->neighborhood,
                        'address_city' => $sale->delivery->city,
                        'address_state' => $sale->delivery->state,
                        'address_country' => $sale->delivery->country,
                        'address_zip_code' => foxutils()->onlyNumbers($sale->delivery->zip_code),
                    ],
                    'products' => $dataProducts,
                ],
                'created_at' => $sale->start_date,
                'updated_at' => Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->toDateTimeString(),
            ];

            $return = $this->sendPost($data, $this->urlOrder);

            if (isset($return['code']) && $return['code'] == 200) {
                $sentStatus = 2;
            } else {
                $sentStatus = 1;
            }
            Whatsapp2Sent::create(
                [
                    'data' => json_encode($data),
                    'response' => json_encode($return),
                    'sent_status' => $sentStatus,
                    'instance_id' => $sale->id,
                    'instance' => 'sale',
                    'event_sale' => 4,
                    'whatsapp2_integration_id' => $this->integrationId,
                ]
            );
        } catch (Exception $e) {
            report($e);
        }
    }

}
