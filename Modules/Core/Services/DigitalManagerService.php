<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Product;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use Modules\Core\Entities\DigitalmanagerSent;

class DigitalManagerService
{
    /**
     * @var
     */
    private $apiUrl;
    /**
     * @var
     */
    private $apiToken;
    /**
     * @var
     */
    private $integrationId;

    /**
     * DigitalManagerService constructor.
     * @param $apiUrl
     * @param $apiToken
     */
    function __construct($apiUrl, $apiToken, $integrationId)
    {
        $this->apiUrl = $apiUrl;
        $this->apiToken = $apiToken;
        $this->integrationId = $integrationId;
    }

    /**
     * @param $data
     * @return bool|string
     */
    private function sendPost($data)
    {
        $data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
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
     * @param Sale $sale
     * @param ProductPlanSale $productPlanSale
     * @param Product $product
     * @param $eventSale
     * @return bool|string
     */
    public function sendSale(Sale $sale, ProductPlanSale $productPlanSale, Product $product, $eventSale)
    {
        try {
            switch ($sale->status) {
                case 1:
                    $status = 'approved';
                    break;
                case 5:
                    $status = 'canceled';
                    break;
                case 4:
                    $status = 'chargeback';
                    break;
                case 2:
                    if ($sale->payment_method == 2)
                        $status = 'billet_printed';
                    else
                        $status = 'waiting_payment';
                    break;
                case 3:
                case 6:
                case 10:
                    $status = 'other';
                    break;
                default:
                    $status = 'other';
                    break;
            }

            $data = [
                'api_token' => $this->apiToken,
                'id' => Hashids::encode($sale->id),
                'payment_method' => ($sale->payment_method == 2) ? 'billet' : 'credit_card', // paypal,bank_transfer
                'status' => $status, // billet_printed,approved,canceled,chargeback,completed,dispute,refunded,waiting_paymen,abandoned,other
                'currency' => (!empty($sale->checkout->currency)) ? $sale->checkout->currency : 'BRL',
                'ordered_at' => Carbon::createFromFormat('Y-m-d H:i:s', $sale->created_at)->toDateTimeString(),
                'approved_at' => (!empty($sale->end_date)) ? Carbon::createFromFormat('Y-m-d H:i:s', $sale->end_date)->toDateTimeString() : null, // required_if:status,approved,completed
                'canceled_at' => ($sale->status == 5) ? Carbon::createFromFormat('Y-m-d H:i:s', $ale->updated_at)->toDateTimeString() : null,  //required_if:status,canceled,refunded
                'warranty_until' => ($sale->status == 1) ? Carbon::createFromFormat('Y-m-d H:i:s', $productPlanSale->created_at)->addDays($productPlanSale->guarantee)->toDateTimeString() : null, // required_with:approved_at
                'unavailable_until' => ($sale->status == 1 && !empty($sale->end_date)) ? Carbon::createFromFormat('Y-m-d H:i:s', $sale->end_date)->toDateTimeString() : null, // required_with:approved_at
                'value' => $this->formatFloatValue($sale->total_paid_value),
                'transaction_fee' => '0.00', // TODO - custo da venda
                'shipping_fee' => $this->formatFloatValue($sale->shipment_value),
                'net_value' => $this->formatFloatValue($sale->total_paid_value), // liquido (value - transaction_fee)
                'source' => $sale->checkout->src ?? null,
                // 'checkout_source'   => $sale->checkout-> ?? null,
                'utm_source' => $sale->checkout->utm_source ?? null,
                'utm_campaing' => $sale->checkout->utm_campaing ?? null,
                'utm_term' => $sale->checkout->utm_term ?? null,
                'utm_medium' => $sale->checkout->utm_medium ?? null,
                'utm_content' => $sale->checkout->utm_content ?? null,
                'billet_url' => $sale->boleto_link,
                'billet_line' => $sale->boleto_digitable_line,
                'product' => [
                    'id' => Hashids::encode($product->id),
                    'name' => $product->name,
                    'qty' => 1, // TODO
                    'cost' => null,
                ],
                'contact' => [
                    'name' => $sale->customer->name,
                    'email' => $sale->customer->email,
                    'doc' => $sale->customer->document,
                    'phone_number' => $sale->customer->telephone,
                    'address' => $sale->delivery->street ?? null,
                    'address_number' => $sale->delivery->number ?? null,
                    'address_comp' => $sale->delivery->complement ?? null,
                    'address_district' => $sale->delivery->neighborhood ?? null,
                    'address_city' => $sale->delivery->city ?? null,
                    'address_country' => substr($sale->delivery->country ?? null, 0, 2),
                    'address_zip_code' => $sale->delivery->zip_code ?? null,
                ],
                // 'affiliate' => [
                //     'id'    => ['nullable', 'string', 'max:191'],
                //     'name'  => ['string', 'max:191', 'required_with:affiliate.id'],
                //     'email' => ['email', 'max:191', 'required_with:affiliate.id'],
                //     'value' => ['numeric', 'min:0', 'required_with:affiliate.id'],
                // ],
                // 'subscription' => [
                //     'id'                 => ['nullable', 'string', 'max:191'],
                //     'name'               => ['string', 'max:191', 'required_with:subscription.id'],
                //     'status'             => ['in:active,canceled,past_due,expired,inactive', 'required_with:subscription.id'],
                //     'charged_times'      => ['numeric', 'min:0', 'required_with:subscription.id'],
                //     'charged_every_days' => ['numeric', 'min:1', 'required_with:subscription.id'],
                //     'started_at'         => ['date', 'required_with:subscription.id'],
                //     'canceled_at'        => ['date', 'required_if:subscription.status,canceled,expired']
                // ],
            ];

            $return = $this->sendPost($data);
            if (isset($return['code']) && $return['code'] == 200) {
                $sentStatus = 2;
            } else {
                $sentStatus = 1;
            }
            DigitalmanagerSent::create(
                [
                    'data' => json_encode($data),
                    'response' => json_encode($return),
                    'sent_status' => $sentStatus,
                    'instance_id' => $sale->id,
                    'instance' => 'sale',
                    'event_sale' => $eventSale,
                    'digitalmanager_integration_id' => $this->integrationId,
                ]
            );
            return $return;
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @param $value
     */
    private function formatFloatValue($value)
    {
        $value = preg_replace('/[^0-9]/', '', $value);
        if (strlen($value) < 3) {
            $lack = 3 - strlen($value);
            for ($i = 0; $i < $lack; $i++) {
                $value = '0' . $value;
            }
        }
        return substr($value, 0, -2) . '.' . substr($value, -2);
    }
}
