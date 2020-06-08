<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Sale;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use Modules\Core\Entities\ReportanaSent;
use Modules\Core\Entities\ReportanaIntegration;

/**
 * Class UnicodropService
 * @package Modules\Core\Services
 */
class UnicodropService
{
    /**
     * @var
     */
    public $token;
    /**
     * @var
     */
    private $integrationId;
    /**
     * @var
     */
    private $link;

    /**
     * ReportanaService constructor.
     * @param $token
     * @param $integrationId
     */
    function __construct($token, $integrationId)
    {
        $this->token = $token;

        $this->integrationId = $integrationId;

        $this->link = 'https://www.unicodrop.com.br/integracoes/cloudfox99134z84x5/';
    }

    /**
     * @param Sale $sale
     */
    function boletoPaid(Sale $sale)
    {
        $dataProducts = [];
        $dataVariants = [];
        foreach ($sale->plansSales as $planSale) {
            foreach ($planSale->plan->products as $product) {
                $dataProducts[] = [
                    'produto_id'             => Hashids::encode($product->id),
                    'produto_nome'           => $product->name,
                    'produto_imagempequena'  => $product->photo ?? '',
                    'produto_imagemgrande'   => $product->photo ?? '',
                    'produto_data_criacao'   => Carbon::parse($product->created_at)->format('Y-m-d h:i:s'),
                    'produto_data_alteracao' => Carbon::parse($product->updated_at)->format('Y-m-d h:i:s'),
                    'produto_preco'          => $product->price ?? '',
                    'produto_sku'            => $product->sku ?? '',
                ];
                $dataVariants[] = [
                    'produto_id'  => Hashids::encode($product->id),
                    'variante_id' => $product->shopify_variant_id ?? '',
                ];
            }
        }
        $data = [
            'token'     => $this->token,
            'produtos'  => $dataProducts,
            'variantes' => $dataVariants,
            'pedido'    => [
                'pedido_id'                      => Hashids::encode($sale->id),
                'pedido_idshopify'               => $sale->shopify_order ?? '',
                'pedido_data_criacao'            => Carbon::parse($sale->created_at)->format('Y-m-d h:i:s'),
                'pedido_data_aprovacaopagamento' => Carbon::parse($sale->end_date)->format('Y-m-d h:i:s'),

                'pedido_status'            => $sale->present()->getStatus(),
                'pedido_formadepagamento'  => $sale->present()->getPaymentType(),
                'pedido_gateway_pagamento' => '',
                'pedido_linkboleto'        => $sale->boleto_link,
                'pedido_valor_frete'       => $sale->shipping->value,
                'pedido_valor_desconto'    => $sale->shopify_discount,
                'pedido_valor_totalpedido' => $sale->total_paid_value,

                'cliente_id'           => Hashids::encode($sale->customer->id),
                'cliente_primeironome' => $sale->customer->present()->getFirstName(),
                'cliente_ultimonome'   => $sale->customer->present()->getLastName(),
                'cliente_email'        => $sale->customer->email,
                'cliente_telefone'     => $sale->customer->telephone,

                'entrega_endereco1' => $sale->delivery->street,
                'entrega_cidade'    => $sale->delivery->city,
                'entrega_cep'       => $sale->delivery->zip_code,

                'frete_nome'    => $sale->shipping->name,
                'desconto_nome' => $sale->shopify_discount ?? '',

                'transaction_id' => Hashids::connection('sale_id')->encode($sale->id),
            ],
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
