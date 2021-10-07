<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Sale;
use Exception;
use Modules\Core\Entities\IntegrationLog;
use Modules\Core\Entities\UnicodropIntegration;

class UnicodropService
{
    const API_ENDPOINT = 'https://www.unicodrop.com.br/integracoes/cloudfox/default.asp?t=1&u=';

    public UnicodropIntegration $integration;

    function __construct(UnicodropIntegration $integration)
    {
        $this->integration = $integration;
    }

    function boletoPaid(Sale $sale)
    {
        try{
            $data = [
                'transaction_id' => hashids_encode($sale->id, 'sale_id'),
                'payment_method' => 'billet',
                'status' => 'paid',
                'name' => $sale->customer->name,
                'phone' => str_replace('+55', '', $sale->customer->telephone),
                'email' => $sale->customer->present()->getEmail(),
                'address' => $sale->delivery->street,
                'address_number' => $sale->delivery->number,
                'address_district' => $sale->delivery->neighborhood,
                'address_zip_code' => $sale->delivery->zip_code,
                'address_city' => $sale->delivery->city,
                'address_state' => $sale->delivery->state,
                'address_country' => 'BR',
                'document' => $sale->customer->document,
                'total_price' => $sale->total_paid_value,
                'billet_url' => $sale->boleto_link,
                'products' => $this->getPlansList($sale),
            ];
            $this->sendPost($data);
        }
        catch(Exception $e) {
            report($e);
        }
    }

    function pixExpired(Sale $sale)
    {
        try{
            $data = [
                'transaction_id' => hashids_encode($sale->id, 'sale_id'),
                'payment_method' => 'pix',
                'status' => 'expired',
                'name' => $sale->customer->name,
                'phone' => str_replace('+55', '', $sale->customer->telephone),
                'email' => $sale->customer->present()->getEmail(),
                'address' => $sale->delivery->street,
                'address_number' => $sale->delivery->number,
                'address_district' => $sale->delivery->neighborhood,
                'address_zip_code' => $sale->delivery->zip_code,
                'address_city' => $sale->delivery->city,
                'address_state' => $sale->delivery->state,
                'address_country' => 'BR',
                'document' => $sale->customer->document,
                'total_price' => $sale->total_paid_value,
                'billet_url' => $sale->boleto_link,
                'products' => $this->getPlansList($sale),
            ];
            $this->sendPost($data);
        }
        catch(Exception $e) {
            report($e);
        }
    }

    private function sendPost($data)
    {
        $curl = curl_init();
        $url = self::API_ENDPOINT . $this->integration->token;

        // $url = 'https://www.unicodrop.com.br/integracoes/cloudfox/default.asp?t=1&u=6F8DD99B-BAC4-4436-9EC1-E0E285987B0B';

        curl_setopt_array($curl,
                          [
                              CURLOPT_URL            => $url,
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_CUSTOMREQUEST  => "POST",
                              CURLOPT_POSTFIELDS     => json_encode($data),
                              CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                          ]
        );

        $response = curl_exec($curl);
        curl_close($curl);

        IntegrationLog::create([
            'source_table' => 'sale',
            'source_id' => '0',
            'request' => json_encode(['url' => $url, 'data' => $data]),
            'response' => $response,
            'api' => 'UnicoDrop',
        ]);
    }

    public function getPlansList(Sale $sale)
    {
        $plans = [];

        foreach ($sale->plansSales as $planSale) {
            $plans[] = [
                "price" => $planSale->plan()->first()->price,
                "quantity" => $planSale->amount,
                "product_name" => $planSale->plan()->first()->name . ' - ' . $planSale->plan()->first()->description,
            ];
        }

        return $plans;
    }
}
