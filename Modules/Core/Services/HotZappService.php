<?php

namespace Modules\Core\Services;

use App\Entities\Sale;
use Illuminate\Support\Facades\Log;

class HotZappService {

    private $link;

    function __construct($link){

        $this->link = $link;
    }

    function newBoleto(Sale $sale) {

        $data = [
            'transaction_id'        => @$sale->id,
            'name'                  => @$sale->client()->name,
            'phone'                 => @$sale->client()->cellphone,
            'email'                 => @$sale->client()->email,
            'address'               => @$sale->delivery()->street,
            'address_number'        => @$sale->delivery()->number,
            'address_district'      => @$sale->delivery()->neighborhood,
            'address_zip_code'      => @$sale->delivery()->zip_code,
            'address_city'          => @$sale->delivery()->city,
            'address_state'         => @$sale->delivery()->state,
            'address_country'       => 'BR',
            'doc'                   => @$sale->client()->document,
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
            'line_items' => [ [
                'product_name' => @$plan->nome,
                'quantity'     => '1',
                'price'        => @$plano->preco,
            ] ]
        ];

        self::sendPost($data,$plano->hotzapp_sale);

    }

    function boletoPaid(Sale $sale) {

        $data = [
            'transaction_id'        => @$venda->id,
            'name'                  => @$comprador->nome,
            'phone'                 => @$comprador->telefone,
            'email'                 => @$comprador->email,
            'address'               => @$entrega->rua,
            'address_number'        => @$entrega->numero,
            'address_district'      => @$entrega->bairro,
            'address_zip_code'      => @$entrega->cep,
            'address_city'          => @$entrega->cidade,
            'address_state'         => @$entrega->estado,
            'address_country'       => 'BR',
            'doc'                   => @$comprador->cpf_cnpj,
            'cms_vendor'            => '',
            'total_price'           => @round((float) $venda->valor_plano,2) + round((float) $venda->valor_frete,2),
            'receiver_type'         => '',
            'cms_aff'               => '',
            'aff'                   => '',
            'aff_name'              => '',
            'billet_url'            => @$venda->link_boleto,
            'transaction_error_msg' => '',
            'paid_at'               => '',
            'payment_method'        => 'billet',
            'financial_status'      => 'paid',
            'risk_level'            => '',
            'line_items' => [ [
                'product_name' => @$plano->nome,
                'quantity'     => '1',
                'price'        => @$plano->preco
            ] ]
        ];

        self::sendPost($data,$plano->hotzapp_sale);

    }

    function creditCardRefused(Sale $sale) {

        $data = [
            'transaction_id'        => @$saleId,
            'name'                  => @$sale->nome,
            'phone'                 => @$sale->telefone,
            'email'                 => @$sale->email,
            'address'               => @$sale->endereco,
            'address_number'        => @$sale->numero-casa,
            'address_district'      => @$sale->bairro,
            'address_zip_code'      => @$sale->cep,
            'address_city'          => @$sale->cidade,
            'address_state'         => @$sale->estado,
            'address_country'       => 'BR',
            'doc'                   => @$sale->cpfcnpj,
            'cms_vendor'            => '',
            'total_price'           => @round((float) $valor_pagamento,2),
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
            'line_items' => [ [
                'product_name' => @$plano->nome,
                'quantity'     => '1',
                'price'        => @$plano->preco,
            ] ]
        ];

        self::sendPost($data,$plano->hotzapp_sale);

    }

    function creditCardPaid(Sale $sale) {

        $data = [
            'transaction_id'        => @$venda->id,
            'name'                  => @$comprador->nome,
            'phone'                 => @$comprador->telefone,
            'email'                 => @$comprador->email,
            'address'               => @$entrega->rua,
            'address_number'        => @$entrega->numero,
            'address_district'      => @$entrega->bairro,
            'address_zip_code'      => @$entrega->cep,
            'address_city'          => @$entrega->cidade,
            'address_state'         => @$entrega->estado,
            'address_country'       => 'BR',
            'doc'                   => @$comprador->cpf_cnpj,
            'cms_vendor'            => '',
            'total_price'           => @round((float) $venda->valor_plano,2) + round((float) $venda->valor_frete,2),
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
            'line_items' => [ [
                'product_name' => @$plano->nome,
                'quantity'     => '1',
                'price'        => @$plano->preco,
            ] ]
        ];

        self::sendPost($data,$plano->hotzapp_sale);

    }

    private function sendPost($data){

        $curl = curl_init();

        curl_setopt_array($curl,
            array(
                CURLOPT_URL => $this->link,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => 
                array(
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

    }

}
