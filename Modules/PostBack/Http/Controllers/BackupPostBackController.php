<?php

namespace Modules\PostBack\Http\Controllers;

use App\User;
use App\Plano;
use App\Venda;
use App\Entrega;
use App\Comprador;
use App\PlanoVenda;
use App\CompraUsuario;
use App\IntegracaoShopify;
use Slince\Shopify\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\HotZapp\HotZapp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Transportadoras\Kapsula;
use Modules\Core\Transportadoras\LiftGold;

class BackupPostBackController extends Controller {

    public function postBackListener(Request $request) {

        $dados = $request->all();

        Log::write('info', 'retorno do pagar.me : '. print_r($dados, true));
 
        if(isset($dados['event']) && $dados['event'] = 'transaction_status_changed'){

            if(isset($dados['transaction']['metadata']['servico'])){

                $compraUsuario = CompraUsuario::find($dados['transaction']['metadata']['id_venda']);

                if($compraUsuario['status'] == $dados['transaction']['status']){
                    return 'Sucesso';
                }

                $compraUsuario->update([
                    'status'        => $dados['transaction']['status'],
                    'plataforma_id' => $dados['id'],
                ]);

                if($dados['transaction']['status'] == 'paid'){

                    $compraUsuario->update([
                        'data_finalizada' => \Carbon\Carbon::now()->subHour()->subHour()
                    ]);
    
                    $user = User::find($compraUsuario['comprador']);

                    $qtdSms = $user['sms_zenvia_qtd'] + $compraUsuario['quantidade'];

                    $user->update([
                        'sms_zenvia_qtd' => $qtdSms
                    ]);
                }
            }
            else{
                $venda = Venda::find($dados['transaction']['metadata']['id_venda']);

                if($venda == null){
                    Log::write('info', 'VENDA NÃO ENCONTRADA!!!');
                    return 'sucesso';
                }

                if($dados['transaction']['status'] == $venda['pagamento_status']){
                    return 'sucesso';
                }

                if($dados['transaction']['status'] == 'paid'){

                    date_default_timezone_set('America/Sao_Paulo');

                    $venda->update([
                        'pagamento_status' => $dados['transaction']['status'],
                        'pagamento_id'     => $dados['id'],
                        'data_finalizada'  => \Carbon\Carbon::now()
                    ]);

                    $comprador = Comprador::find($venda['comprador']);
                    $planoVenda = PlanoVenda::where('venda', $venda['id'])->first();

                    $plano = Plano::find($planoVenda->plano);

                    if($plano['shopify_id'] == ''){
                        $entrega = Entrega::find($venda['entrega']);

                        if($venda->forma_pagamento == 'boleto' && $plano['hotzapp_dados'] != null){

                            HotZapp::boletoPago($plano,$venda,$entrega,$comprador);
                        }

                        if($venda->forma_pagamento == 'cartao_credito' && $plano['hotzapp_dados'] != null) {

                            // $venda = Venda::where([
                            //     'comprador'           => $comprador['id'],
                            //     'plano'               => $plano['id'],
                            //     'mercado_pago_status' => 'rejected'
                            // ])->first();

                            // if($venda != null){

                            //     HotZapp::cartaoPago($plano,$venda,$entrega,$comprador);
                            // }
                        }
    
                        if($plano['transportadora'] == 1) {
                            $clienteId = Kapsula::cadastarCliente($entrega, $comprador);
                            $response = Kapsula::realizarPedido($clienteId, $plano['id_plano_trasnportadora']);
                            $entrega->update($response);
                        }
                        if($plano['transportadora'] == 3) {
                            $response = LiftGold::realizarPedido($venda, $plano, $entrega, $comprador);
                            $entrega->update($response);
                        }
                    }
                    else{

                        $integracaoShopify = IntegracaoShopify::where('projeto',$plano['projeto'])->first();

                        $planosVenda = PlanoVenda::where('venda', $venda['id'])->get()->toArray();

                        try{
                            $credential = new PublicAppCredential($integracaoShopify['token']);

                            $client = new Client($credential, $integracaoShopify['url_loja'], [
                                'metaCacheDir' => './tmp'
                            ]);

                            $nomes = explode(" ",$comprador['nome']);
                            $telefone = str_replace("+",'',$comprador['telefone']);

                            $telefone = "+55".$telefone;
                            if(strlen($telefone) != 14){
                                $telefone = "+557734881234";
                            }

                            $items = [];

                            foreach($planosVenda as $planoVenda){
                                $plano = Plano::find($planoVenda['plano']);
                
                                $items[] = [
                                    "grams"             => 500,
                                    "id"                => $plano['id'],
                                    "price"             => $plano['preco'],
                                    "product_id"        => $plano['shopify_id'],
                                    "quantity"          => $planoVenda['quantidade'],
                                    "requires_shipping" => true,
                                    "sku"               => $plano['nome'],
                                    "title"             => $plano['nome'],
                                    "variant_id"        => $plano['shopify_variant_id'],
                                    "variant_title"     => $plano['nome'],
                                    "name"              => $plano['nome'],
                                    "gift_card"         => false,
                                ];
                            }

                            $entrega = Entrega::find($venda['entrega']);

                            $address = $entrega['rua'] . ' - ' . $entrega['numero'];
                            if($entrega['ponto_referencia'] != ''){
                                $address .= ' - ' . $entrega['ponto_referencia'];
                            }
                            $address .= ' - ' .$entrega['bairro'];

                            $shippingAddress = [
                                "address1"      => $address,
                                "address2"      => "",
                                "city"          => $entrega['cidade'],
                                "company"       => $comprador['cpf'],
                                "country"       => "Brasil",
                                "first_name"    => $nomes[0],
                                "last_name"     => $nomes[count($nomes) - 1],
                                "phone"         => $telefone,
                                "province"      => $entrega['estado'],
                                "zip"           => $entrega['cep'],
                                "name"          => $comprador['nome'],
                                "country_code"  => "BR",
                                "province_code" => $entrega['estado']
                            ];

                            $order = $client->getOrderManager()->create([
                                "accepts_marketing"       => false,
                                "currency"                => "BRL",
                                "email"                   => $comprador['email'],
                                "first_name"              => $nomes[0],
                                "last_name"               => $nomes[count($nomes) - 1],
                                "buyer_accepts_marketing" => false,
                                "line_items"              => $items,
                                "shipping_address"        => $shippingAddress,
                            ]);
                        }
                        catch(\Exception $e){
                            Log::write('info', 'erro ao gerar pedido no shopify com a venda '.$venda['id']);
                            Log::write('info',  print_r($e, true) );
                        }

                    }
                }
                else{
                    $venda->update([
                        'pagamento_status' => $dados['transaction']['status'],
                        'pagamento_id'     => $dados['id'],
                    ]);
                }
            }
        }
        return 'sucesso';
    }

}

