<?php

namespace Modules\Core\Services;

use App\Entities\Sale;
use Illuminate\Support\Facades\Log;

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
     * @param $plans
     */
    function newBoleto(Sale $sale, $plans)
    {

        $data = [
            'transaction_id'        => @$sale->id,
            'name'                  => @$sale->client()->first()->name,
            'phone'                 => @$sale->client()->first()->cellphone,
            'email'                 => @$sale->client()->first()->email,
            'address'               => @$sale->delivery()->first()->street,
            'address_number'        => @$sale->delivery()->first()->number,
            'address_district'      => @$sale->delivery()->first()->neighborhood,
            'address_zip_code'      => @$sale->delivery()->first()->zip_code,
            'address_city'          => @$sale->delivery()->first()->city,
            'address_state'         => @$sale->delivery()->first()->state,
            'address_country'       => 'BR',
            'doc'                   => @$sale->client()->first()->document,
            'cms_vendor'            => '',
            'total_price'           => @$sale->total_paid_value,
            'receiver_type'         => '',
            'cms_aff'               => '',
            'aff'                   => '',
            'aff_name'              => '',
            'billet_url'            => @$sale->boleto_link,
            'billet_barcode'        => @$sale->boleto_digitable_line,
            'transaction_error_msg' => '',
            'paid_at'               => '',
            'payment_method'        => 'billet',
            'financial_status'      => 'issued',
            'risk_level'            => '',
            'line_items'            => $plans,
        ];

        self::sendPost($data);
    }

    /**
     * @param Sale $sale
     * @param $plans
     */
    function boletoPaid(Sale $sale, $plans)
    {

        $data = [
            'transaction_id'        => @$sale->id,
            'name'                  => @$sale->client()->first()->name,
            'phone'                 => @$sale->client()->first()->cellphone,
            'email'                 => @$sale->client()->first()->email,
            'address'               => @$sale->delivery()->first()->street,
            'address_number'        => @$sale->delivery()->first()->number,
            'address_district'      => @$sale->delivery()->first()->neighborhood,
            'address_zip_code'      => @$sale->delivery()->first()->zip_code,
            'address_city'          => @$sale->delivery()->first()->city,
            'address_state'         => @$sale->delivery()->first()->state,
            'address_country'       => 'BR',
            'doc'                   => @$sale->client()->first()->document,
            'cms_vendor'            => '',
            'total_price'           => @$sale->total_paid_value,
            'receiver_type'         => '',
            'cms_aff'               => '',
            'aff'                   => '',
            'aff_name'              => '',
            'billet_url'            => @$sale->boleto_link,
            'transaction_error_msg' => '',
            'paid_at'               => '',
            'payment_method'        => 'billet',
            'financial_status'      => 'paid',
            'risk_level'            => '',
            'line_items'            => $plans,
        ];

        self::sendPost($data);
    }

    /**
     * @param Sale $sale
     * @param $plans
     */
    function creditCardRefused(Sale $sale, $plans)
    {

        $data = [
            'transaction_id'        => @$sale->id,
            'name'                  => @$sale->client()->first()->name,
            'phone'                 => @$sale->client()->first()->cellphone,
            'email'                 => @$sale->client()->first()->email,
            'address'               => @$sale->delivery()->first()->street,
            'address_number'        => @$sale->delivery()->first()->number,
            'address_district'      => @$sale->delivery()->first()->neighborhood,
            'address_zip_code'      => @$sale->delivery()->first()->zip_code,
            'address_city'          => @$sale->delivery()->first()->city,
            'address_state'         => @$sale->delivery()->first()->state,
            'address_country'       => 'BR',
            'doc'                   => @$sale->client()->first()->document,
            'cms_vendor'            => '',
            'total_price'           => @$sale->total_paid_value,
            'receiver_type'         => '',
            'cms_aff'               => '',
            'aff'                   => '',
            'aff_name'              => '',
            'billet_url'            => '',
            'billet_barcode'        => '',
            'transaction_error_msg' => '',
            'paid_at'               => '',
            'payment_method'        => 'credit',
            'financial_status'      => 'refused',
            'risk_level'            => '',
            'line_items'            => $plans,
        ];

        self::sendPost($data);
    }

    /**
     * @param Sale $sale
     * @param $plans
     */
    function creditCardPaid(Sale $sale, $plans)
    {

        $data = [
            'transaction_id'        => @$sale->id,
            'name'                  => @$sale->client()->first()->name,
            'phone'                 => @$sale->client()->first()->cellphone,
            'email'                 => @$sale->client()->first()->email,
            'address'               => @$sale->delivery()->first()->street,
            'address_number'        => @$sale->delivery()->first()->number,
            'address_district'      => @$sale->delivery()->first()->neighborhood,
            'address_zip_code'      => @$sale->delivery()->first()->zip_code,
            'address_city'          => @$sale->delivery()->first()->city,
            'address_state'         => @$sale->delivery()->first()->state,
            'address_country'       => 'BR',
            'doc'                   => @$sale->client()->first()->document,
            'cms_vendor'            => '',
            'total_price'           => @$sale->total_paid_value,
            'receiver_type'         => '',
            'cms_aff'               => '',
            'aff'                   => '',
            'aff_name'              => '',
            'billet_url'            => '',
            'transaction_error_msg' => '',
            'paid_at'               => '',
            'payment_method'        => 'credit',
            'financial_status'      => 'paid',
            'risk_level'            => '',
            'line_items'            => $plans,
        ];

        self::sendPost($data);
    }

    /**
     * @param Sale $sale
     * @param $plans
     */
    function abandonedCart(Sale $sale, $plans)
    {

        $data = [
            'transaction_id'        => @$sale->id,
            'name'                  => @$sale->client()->first()->name,
            'phone'                 => @$sale->client()->first()->cellphone,
            'email'                 => @$sale->client()->first()->email,
            'address'               => @$sale->delivery()->first()->street,
            'address_number'        => @$sale->delivery()->first()->number,
            'address_district'      => @$sale->delivery()->first()->neighborhood,
            'address_zip_code'      => @$sale->delivery()->first()->zip_code,
            'address_city'          => @$sale->delivery()->first()->city,
            'address_state'         => @$sale->delivery()->first()->state,
            'address_country'       => 'BR',
            'doc'                   => @$sale->client()->first()->document,
            'cms_vendor'            => '',
            'total_price'           => @$sale->total_paid_value,
            'receiver_type'         => '',
            'cms_aff'               => '',
            'aff'                   => '',
            'aff_name'              => '',
            'billet_url'            => '',
            'transaction_error_msg' => '',
            'paid_at'               => '',
            'payment_method'        => 'credit',
            'financial_status'      => 'paid',
            'risk_level'            => '',
            'line_items'            => $plans,
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
}




