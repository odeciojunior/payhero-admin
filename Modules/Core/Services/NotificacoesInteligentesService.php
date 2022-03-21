<?php

namespace Modules\Core\Services;

use Exception;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\PixCharge;
use Modules\Core\Entities\Sale;

class NotificacoesInteligentesService
{
    private $link;

    function __construct($link)
    {
        $this->link = $link;
    }

    

    
    private function sendPost($data)
    {

        try {
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
        } catch (Exception $e) {
            report($e);
        }
    }


    /**
     * @param Sale $sale
     */
    function pixExpired(Sale $sale)
    {
        

        $pixCharge = PixCharge::where('sale_id',$sale->id)
        ->orderBy('id','DESC')->first();
        $data = [
            'transaction_id' => Hashids::connection('sale_id')->encode($sale->id),
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
            'doc' => $sale->customer->document,
            'cms_vendor' => '',
            'total_price' => $sale->total_paid_value,
            'receiver_type' => '',
            'cms_aff' => '',
            'aff' => '',
            'aff_name' => '',
            'pix_qrcode' => '',//$pixCharge->qrcode,
            'transaction_error_msg' => '',
            'paid_at' => '',
            'payment_method' => 'pix',
            'financial_status' => 'expired',
            'risk_level' => '',
            'line_items' => $sale->present()->getHotBilletPlansList(),
        ];

        self::sendPost($data);
    }
}

