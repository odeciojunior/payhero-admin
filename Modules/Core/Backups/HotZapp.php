<?php

namespace Modules\Core\HotZapp;

use Illuminate\Support\Facades\Log;
use Modules\Checkout\Entities\Plano;
use Modules\Checkout\Entities\DadosHotzapp;

class HotZapp {

    static function boletoEmitido($id_venda,$dados,$valor_pagamento,$link_boleto,$codigo_barra,Plano $plano) {

        $cr = curl_init();

        $data = [
            'transaction_id'        => @$id_venda,
            'name'                  => @$dados['nome'],
            'phone'                 => @$dados['telefone'],
            'email'                 => @$dados['email'],
            'address'               => @$dados['endereco'],
            'address_number'        => @$dados['numero-casa'],
            'address_district'      => @$dados['bairro'],
            'address_zip_code'      => @$dados['cep'],
            'address_city'          => @$dados['cidade'],
            'address_state'         => @$dados['estado'],
            'address_country'       => 'BR',
            'doc'                   => @$dados['cpfcnpj'],
            'cms_vendor'            => '',
            'total_price'           => @round((float) $valor_pagamento,2),
            'receiver_type'         => '',
            'cms_aff'               => '',
            'aff'                   => '',
            'aff_name'              => '',
            'billet_url'            => @$link_boleto,
            'billet_barcode'        => $codigo_barra,
            'transaction_error_msg' => '',
            'paid_at'               => '',
            'payment_method'        => 'billet',
            'financial_status'      => 'issued',
            'risk_level'            => '',
            'line_items' => [ [
                'product_name' => @$plano->nome,
                'quantity'     => '1',
                'price'        => @$plano->preco,
            ] ]
        ];

        self::sendPost($data,$plano->hotzapp_dados);

    }

    static function erroPagamentoCartaoCredito($id_venda,$dados,$valor_pagamento,Plano $plano) {

        $cr = curl_init();

        $data = [
            'transaction_id'        => @$id_venda,
            'name'                  => @$dados['nome'],
            'phone'                 => @$dados['telefone'],
            'email'                 => @$dados['email'],
            'address'               => @$dados['endereco'],
            'address_number'        => @$dados['numero-casa'],
            'address_district'      => @$dados['bairro'],
            'address_zip_code'      => @$dados['cep'],
            'address_city'          => @$dados['cidade'],
            'address_state'         => @$dados['estado'],
            'address_country'       => 'BR',
            'doc'                   => @$dados['cpfcnpj'],
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

        self::sendPost($data,$plano->hotzapp_dados);

    }

    static function boletoPago($plano,$venda,$entrega,$comprador) {

        $cr = curl_init();

        $data = [
            'transaction_id'        => @$venda->id,
            'name'                  => @$comprador['nome'],
            'phone'                 => @$comprador['telefone'],
            'email'                 => @$comprador['email'],
            'address'               => @$entrega['rua'],
            'address_number'        => @$entrega['numero'],
            'address_district'      => @$entrega['bairro'],
            'address_zip_code'      => @$entrega['cep'],
            'address_city'          => @$entrega['cidade'],
            'address_state'         => @$entrega['estado'],
            'address_country'       => 'BR',
            'doc'                   => @$comprador['cpf_cnpj'],
            'cms_vendor'            => '',
            'total_price'           => @round((float) $venda->valor_plano,2) + round((float) $venda->valor_frete,2),
            'receiver_type'         => '',
            'cms_aff'               => '',
            'aff'                   => '',
            'aff_name'              => '',
            'billet_url'            => @$venda['link_boleto'],
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

        self::sendPost($data,$plano->hotzapp_dados);

    }

    static function cartaoPago($plano,$venda,$entrega,$comprador) {

        $data = [
            'transaction_id'        => @$venda->id,
            'name'                  => @$comprador['nome'],
            'phone'                 => @$comprador['telefone'],
            'email'                 => @$comprador['email'],
            'address'               => @$entrega['rua'],
            'address_number'        => @$entrega['numero'],
            'address_district'      => @$entrega['bairro'],
            'address_zip_code'      => @$entrega['cep'],
            'address_city'          => @$entrega['cidade'],
            'address_state'         => @$entrega['estado'],
            'address_country'       => 'BR',
            'doc'                   => @$comprador['cpf_cnpj'],
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

        self::sendPost($data,$plano->hotzapp_dados);

    }

    private static function sendPost($data, $id_hotzapp){

        $link_hotzapp = DadosHotzapp::find($id_hotzapp)->link;

        $curl = curl_init();

        curl_setopt_array($curl,
            array(
                CURLOPT_URL => $link_hotzapp,
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

