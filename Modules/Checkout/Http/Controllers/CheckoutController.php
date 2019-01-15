<?php

namespace Modules\Checkout\Http\Controllers;

use PagarMe\Client;
use App\CompraUsuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CheckoutController extends Controller {


    public function checkout(Request $request) {

        $dados = $request->all();

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
            $encryptionKey = getenv('PAGAR_ME_ENCRYPTION_KEY_PRODUCAO');
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
            $encryptionKey = getenv('PAGAR_ME_ENCRYPTION_KEY_SANDBOX');
        }

        \JavaScript::put([
            'valor_compra' => $dados['valor'],
            'encryption_key' => $encryptionKey,
            'path_amex_card' => asset('assets/img/amex.svg'),
            'path_diners_card' => asset('assets/img/diners.svg'),
            'path_elo_card' => asset('assets/img/elo.svg'),
            'path_hipercard_card' => asset('assets/img/hipercard.svg'),
            'path_master_card' => asset('assets/img/master.svg'),
            'path_visa_card' => asset('assets/img/visa.svg'),
        ]);

        return view('checkout::checkout',[
            'servico' => $dados['servico'],
            'valor' => $dados['valor'],
            'quantidade' => $dados['quantidade']
        ]);
    }

    public function pagamentoCartao(Request $request) {

        $dados = $request->all();

        $qtd_parcelas = $dados['parcelas'];

        if($qtd_parcelas == '' || !is_numeric($qtd_parcelas) || $qtd_parcelas < 1) {
            $retorno = [
                'sucesso' => false,
                'erro'    => true,
                'mensagem'   => 'PARCELAS INVÁLIDAS !'
            ];
            return response()->json($retorno);
        }

        $valor_total = $dados['valor'];

        $dados_parcelas = $this->getValorTotalParcela($valor_total, $qtd_parcelas);
        $dados_parcelas = explode('##',$dados_parcelas);

        $valor_total = $dados_parcelas[0];
        $valor_parcelas = $dados_parcelas[1];

        $dados['cpf'] = str_replace("-", "", $dados['cpf']);
        $dados['cpf'] = str_replace(".", "", $dados['cpf']);

        $dados['telefone'] = '+'.preg_replace("/[^0-9]/", "", $dados['telefone']);

        $user = \Auth::user();

        $compra_array = [
            'forma_pagamento' => 'Cartão de crédito',
            'valor_total_pago' => substr_replace($valor_total, '.',strlen($valor_total) - 2, 0 ),
            'data_inicio' => \Carbon\Carbon::now()->addHour()->addHour(),
            'comprador' => $user->id,
            'pagamento_id' => '',
            'pagamento_status' => '',
            'item' => 'SMS',
            'quantidade' => @$dados['quantidade'],
            'qtd_parcela' => $qtd_parcelas,
            'valor_parcela' =>  substr_replace($valor_parcelas, '.',strlen($valor_parcelas) - 2, 0 ),
            'bandeira' => @$dados['paymentMethodId'],
        ];

        $compra = CompraUsuario::create($compra_array);

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $transaction = $pagarMe->transactions()->create([
            'amount' => $valor_total,
            'payment_method' => 'credit_card',
            'installments' => $qtd_parcelas,
            'card_hash' => $dados['card_hash'],
            'soft_descriptor' => 'Cloudfox',
            "postback_url" => "https://cloudfox.app/postback",
            'customer' => [
                'external_id' => '#'.$user['id'],
                'name' => $dados['card_name'],
                'type' => 'individual',
                'country' => 'br',
                'documents' => [
                  [
                    'type' => 'cpf',
                    'number' => $dados['card_cpf']
                  ]
                ],
                'phone_numbers' => [ $dados['telefone'] ],
                'email' => $dados['email']
            ],
            'billing' => [
                'name' => $dados['card_name'],
                'address' => [
                    "country" => "br",
                    "street" => 'sem frete',
                    "street_number" => '1000',
                    "state" =>  'sem frete',
                    "city" =>  'sem frete',
                    "neighborhood" =>  'sem frete',
                    "zipcode" =>  '96400300'
                ]
            ],
            'shipping' => [
                'name' => $dados['nome'],
                'fee' => 0,
                'delivery_date' => '2019-12-22',
                'expedited' => false,
                'address' => [
                    "country" => "br",
                    "street" =>  'sem frete',
                    "street_number" => '1000',
                    "state" =>  'sem frete',
                    "city" =>  'sem frete',
                    "neighborhood" =>  'sem frete',
                    "zipcode" =>  '96400300'
                ]
            ],
            'items' => [
                [
                  'id' => '#5',
                  'title' => 'SMS',
                  'unit_price' => str_replace('.','',$valor_total),
                  'quantity' => 1,
                  'tangible' => true
                ],
            ],
            'metadata' => [
                'id_venda' => $compra['id'],
                'servico' => 'SMS',
            ]
        ]);

        $compra->update([
            'plataforma_id' => $transaction->tid,
            'status' => $transaction->status
        ]);

        if($transaction->status == 'refused'){

            $retorno = [
                'sucesso' => false,
                'erro'    => true,
                'mensagem'   => 'CARTÃO RECUSADO !'
            ];

            return response()->json($retorno);
        }

        $retorno = [
            'sucesso' => true,
        ];

        return response()->json($retorno);

    }

    public function pagamentoBoleto(Request $request) {

        $dados = $request->all();

        $valor_total = str_replace('.','',$dados['valor']);

        $user = \Auth::user();

        $dados['cpf'] = str_replace("-", "", $dados['cpf']);
        $dados['cpf'] = str_replace(".", "", $dados['cpf']);

        $dados['telefone'] = '+'.preg_replace("/[^0-9]/", "", $dados['telefone']);

        $compra_array = [
            'forma_pagamento' => 'Boleto',
            'valor_total_pago' => substr_replace($valor_total, '.',strlen($valor_total) - 2, 0 ),
            'data_inicio' => \Carbon\Carbon::now()->addHour()->addHour(),
            'comprador' => $user->id,
            'pagamento_id' => '',
            'pagamento_status' => '',
            'item' => 'SMS',
            'quantidade' => @$dados['quantidade'],
            'bandeira' => @$dados['paymentMethodId'],
        ];

        $compra = CompraUsuario::create($compra_array);

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $transaction = $pagarMe->transactions()->create([
            'amount' => $valor_total,
            'payment_method' => 'boleto',
            "postback_url" => "https://cloudfox.app/postback",
            'customer' => [
                'external_id' => '#'.$compra['id'],
                'name' => $dados['nome'],
                'type' => 'individual',
                'country' => 'br',
                'documents' => [
                  [
                    'type' => 'cpf',
                    'number' => $dados['cpf']
                  ]
                ],
                'phone_numbers' => [ $dados['telefone'] ],
                'email' => $dados['email']
            ],
            'billing' => [
                'name' => $dados['nome'],
                'address' => [
                    "country" => "br",
                    "street" => 'sem frete',
                    "street_number" => '1000',
                    "state" =>  'sem frete',
                    "city" =>  'sem frete',
                    "neighborhood" =>  'sem frete',
                    "zipcode" =>  '96400300'
                ]
            ],
            'shipping' => [
                'name' => $dados['nome'],
                'fee' => 0,
                'delivery_date' => '2019-12-22',
                'expedited' => false,
                'address' => [
                    "country" => "br",
                    "street" =>  'sem frete',
                    "street_number" => '1000',
                    "state" =>  'sem frete',
                    "city" =>  'sem frete',
                    "neighborhood" =>  'sem frete',
                    "zipcode" =>  '96400300'
                ]
            ],
            'items' => [
                [
                  'id' => '#5',
                  'title' => 'SMS',
                  'unit_price' => str_replace('.','',$valor_total),
                  'quantity' => 1,
                  'tangible' => true
                ],
            ],
            'metadata' => [
                'id_venda' => $compra['id'],
                'servico' => 'SMS',
            ]
        ]);

        $link_boleto = $transaction->boleto_url;
        $codigo_barras_boleto = $transaction->boleto_barcode;

        $compra_array = [
            'plataforma_id' => $transaction->tid,
            'status' => $transaction->status,
            'linha_digitavel_boleto' => $codigo_barras_boleto,
            'link_boleto' => $link_boleto,
        ];

        $compra->update($compra_array);

        $retorno = [
            'sucesso' => true,
        ];

        return response()->json($retorno);

    }
    
    public function getParcelas(Request $request){

        $dados = $request->all();

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $amount = $dados['valor_total'] * 100;
        $rate = getenv('TAXA_PARCELAMENTO');
        $rateFreeInstallments = 1;
        $maxInstallments = 10;

        $installments = $pagarMe->transactions()->calculateInstallments([
            'amount' => $amount,
            'free_installments' => $rateFreeInstallments,
            'max_installments' => $maxInstallments,
            'interest_rate' => $rate
        ]);

        $installments_array = json_decode(json_encode($installments,true), true); 

        foreach($installments_array['installments'] as &$installment){

            $installment['amount'] = number_format($installment['amount'] / 100, 2, ',', '.');
            $installment['installment_amount'] = number_format($installment['installment_amount'] / 100, 2, ',', '.');
        }

        return response()->json($installments_array);
    }

    public function getValorTotalParcela($valor, $qtd_parcelas){

        if(getenv('PAGAR_ME_PRODUCAO') == 'true'){
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_PRODUCAO'));
        }
        else{
            $pagarMe = new Client(getenv('PAGAR_ME_PUBLIC_KEY_SANDBOX'));
        }

        $amount = str_replace('.','',$valor);
        $rate = getenv('TAXA_PARCELAMENTO');
        $rateFreeInstallments = 1;
        $maxInstallments = 10;

        $installments = $pagarMe->transactions()->calculateInstallments([
            'amount' => $amount,
            'free_installments' => $rateFreeInstallments,
            'max_installments' => $maxInstallments,
            'interest_rate' => $rate
        ]);

        $installments_array = json_decode(json_encode($installments,true), true); 

        foreach($installments_array['installments'] as $installment){

            if($installment['installment'] == $qtd_parcelas){
                return $installment['amount']. '##' .$installment['installment_amount'];
            }
        }

        return 'erro';
    }

}
