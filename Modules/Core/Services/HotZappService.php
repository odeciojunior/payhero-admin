<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Sale;
use Vinkla\Hashids\Facades\Hashids;

class HotZappService
{
    /**
     * @var
     */
    private $link;

    /**
     * HotZappService constructor.
     * @param $link
     */
    function __construct($link)
    {
        $this->link = $link;
    }

    /**
     * @param Sale $sale
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    function boletoPaid(Sale $sale)
    {
        $data = [
            'transaction_id'        => Hashids::connection('sale_id')->encode($sale->id),
            'name'                  => $sale->client->name,
            'phone'                 => $sale->client->telephone,
            'email'                 => $sale->client->email,
            'address'               => $sale->delivery->street,
            'address_number'        => $sale->delivery->number,
            'address_district'      => $sale->delivery->neighborhood,
            'address_zip_code'      => $sale->delivery->zip_code,
            'address_city'          => $sale->delivery->city,
            'address_state'         => $sale->delivery->state,
            'address_country'       => 'BR',
            'doc'                   => $sale->client->document,
            'cms_vendor'            => '',
            'total_price'           => $sale->total_paid_value,
            'receiver_type'         => '',
            'cms_aff'               => '',
            'aff'                   => '',
            'aff_name'              => '',
            'billet_url'            => $sale->boleto_link,
            'transaction_error_msg' => '',
            'paid_at'               => '',
            'payment_method'        => 'billet',
            'financial_status'      => 'paid',
            'risk_level'            => '',
            'line_items'            => $this->getHotzappPlansList($sale),
        ];

        self::sendPost($data);
    }

    /**
     * @param $data
     */
    private function sendPost($data)
    {

        $curl = curl_init();

        curl_setopt_array($curl,
                          [
                              CURLOPT_URL            => $this->link,
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_CUSTOMREQUEST  => "POST",
                              CURLOPT_POSTFIELDS     => json_encode($data),
                              CURLOPT_HTTPHEADER     =>
                                  [
                                      'Content-Type: application/json',
                                  ],
                          ]
        );

        $response = curl_exec($curl);

        curl_close($curl);
    }

    /**
     * @param Sale $sale
     * @return array
     */
    public function getHotzappPlansList(Sale $sale)
    {
        $plans = [];
        /** @var PlanSale $planSale */
        foreach ($sale->plansSales as $planSale) {
            $plans[] = [
                "price"        => $planSale->plan()->first()->price,
                "quantity"     => $planSale->amount,
                "product_name" => $planSale->plan()->first()->name,
            ];
        }

        return $plans;
    }
}




