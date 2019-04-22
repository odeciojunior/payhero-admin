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

                $compra_usuario = CompraUsuario::find($dados['transaction']['metadata']['id_venda']);

                if($compra_usuario['status'] == $dados['transaction']['status']){
                    return 'Sucesso';
                }

                $compra_usuario->update([
                    'status' => $dados['transaction']['status'],
                    'plataforma_id' => $dados['id'],
                ]);

                if($dados['transaction']['status'] == 'paid'){

                    $compra_usuario->update([
                        'data_finalizada' => \Carbon\Carbon::now()->subHour()->subHour()
                    ]);
    
                    $user = User::find($compra_usuario['comprador']);

                    $qtd_sms = $user['sms_zenvia_qtd'] + $compra_usuario['quantidade'];

                    $user->update([
                        'sms_zenvia_qtd' => $qtd_sms
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
                        'pagamento_id' => $dados['id'],
                        'data_finalizada' => \Carbon\Carbon::now()
                    ]);

                    $comprador = Comprador::find($venda['comprador']);
                    $plano_venda = PlanoVenda::where('venda', $venda['id'])->first();

                    $plano = Plano::find($plano_venda->plano);

                    if($plano['shopify_id'] == ''){
                        $entrega = Entrega::find($venda['entrega']);

                        if($venda->forma_pagamento == 'boleto' && $plano['hotzapp_dados'] != null){

                            HotZapp::boletoPago($plano,$venda,$entrega,$comprador);
                        }

                        if($venda->forma_pagamento == 'cartao_credito' && $plano['hotzapp_dados'] != null) {

                            // $venda = Venda::where([
                            //     'comprador' => $comprador['id'],
                            //     'plano' => $plano['id'],
                            //     'mercado_pago_status' => 'rejected'
                            // ])->first();

                            // if($venda != null){

                            //     HotZapp::cartaoPago($plano,$venda,$entrega,$comprador);
                            // }
                        }
    
                        if($plano['transportadora'] == 1) {
                            $cliente_id = Kapsula::cadastarCliente($entrega, $comprador);
                            $response = Kapsula::realizarPedido($cliente_id, $plano['id_plano_trasnportadora']);
                            $entrega->update($response);
                        }
                        if($plano['transportadora'] == 3) {
                            $response = LiftGold::realizarPedido($venda, $plano, $entrega, $comprador);
                            $entrega->update($response);
                        }
                    }
                    else{

                        $integracao_shopify = IntegracaoShopify::where('projeto',$plano['projeto'])->first();

                        $planos_venda = PlanoVenda::where('venda', $venda['id'])->get()->toArray();

                        try{
                            $credential = new PublicAppCredential($integracao_shopify['token']);

                            $client = new Client($credential, $integracao_shopify['url_loja'], [
                                'metaCacheDir' => './tmp'
                            ]);

                            $nomes = explode(" ",$comprador['nome']);
                            $telefone = str_replace("+",'',$comprador['telefone']);

                            $telefone = "+55".$telefone;
                            if(strlen($telefone) != 14){
                                $telefone = "+557734881234";
                            }

                            $items = [];

                            foreach($planos_venda as $plano_venda){
                                $plano = Plano::find($plano_venda['plano']);
                
                                $items[] = [
                                    "grams" => 500,
                                    "id" => $plano['id'],
                                    "price" => $plano['preco'],
                                    "product_id" => $plano['shopify_id'],
                                    "quantity" => $plano_venda['quantidade'],
                                    "requires_shipping" => true,
                                    "sku" => $plano['nome'],
                                    "title" => $plano['nome'],
                                    "variant_id" => $plano['shopify_variant_id'],
                                    "variant_title" => $plano['nome'],
                                    "name" => $plano['nome'],
                                    "gift_card" => false,
                                ];
                            }

                            $entrega = Entrega::find($venda['entrega']);

                            $address = $entrega['rua'] . ' - ' . $entrega['numero'];
                            if($entrega['ponto_referencia'] != ''){
                                $address .= ' - ' . $entrega['ponto_referencia'];
                            }
                            $address .= ' - ' .$entrega['bairro'];

                            $shipping_address = [
                                "address1"=> $address,
                                "address2"=> "",
                                "city"=> $entrega['cidade'],
                                "company"=> $comprador['cpf'],
                                "country"=> "Brasil",
                                "first_name"=> $nomes[0],
                                "last_name"=> $nomes[count($nomes) - 1],
                                "phone"=> $telefone,
                                "province"=> $entrega['estado'],
                                "zip"=> $entrega['cep'],
                                "name"=> $comprador['nome'],
                                "country_code"=> "BR",
                                "province_code"=> $entrega['estado']
                            ];

                            $order = $client->getOrderManager()->create([
                                "accepts_marketing" => false,
                                "currency" => "BRL",
                                "email" => $comprador['email'],
                                "first_name" => $nomes[0],
                                "last_name" => $nomes[count($nomes) - 1],
                                "buyer_accepts_marketing" => false,
                                "line_items" => $items,
                                "shipping_address" => $shipping_address,
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
                        'pagamento_id' => $dados['id'],
                    ]);
                }
            }
        }
        return 'sucesso';
    }

}

