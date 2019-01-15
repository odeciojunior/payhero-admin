<?php

namespace Modules\PostBack\Http\Controllers;

use App\User;
use App\Plano;
use App\Venda;
use App\Entrega;
use App\Comprador;
use App\PlanoVenda;
use App\CompraUsuario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\HotZapp\HotZapp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Transportadoras\Kapsula;
use Modules\Core\Transportadoras\LiftGold;

class PostBackController extends Controller {

    public function postBackListener(Request $request) {

        $dados = $request->all();

        Log::write('info', 'retorno do pagar.me : '. print_r($dados, true));

        if(isset($dados['event']) && $dados['event'] = 'transaction_status_changed'){

            if($dados['transaction']['metadata']['servico']){

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

                if($dados['transaction']['status'] == 'paid' && $venda['status']){
                    date_default_timezone_set('America/Sao_Paulo');
                    $venda->update([
                        'pagamento_status' => $dados['transaction']['status'],
                        'pagamento_id' => $dados['id'],
                        'data_finalizada' => \Carbon\Carbon::now()->subHour()->subHour()
                    ]);

                    $comprador = Comprador::find($venda['comprador']);//
                    $plano_venda = PlanoVenda::where('venda', $venda['id'])->first();

                    $plano = Plano::find($plano_venda->plano);

                    $entrega = Entrega::find($venda['entrega']);

                    if($venda->forma_pagamento == 'boleto' && $plano->hotzapp_dados != null){

                        HotZapp::boletoPago($plano,$venda,$entrega,$comprador);
                    }

                    if($venda->forma_pagamento == 'cartao_credito' && $plano->hotzapp_dados != null) {

                        // $venda = Venda::where([
                        //     'comprador' => $comprador['id'],
                        //     'plano' => $plano->id,
                        //     'mercado_pago_status' => 'rejected'
                        // ])->first();

                        // if($venda != null){

                        //     HotZapp::cartaoPago($plano,$venda,$entrega,$comprador);
                        // }
                    }

                    if($plano->transportadora == 1) {
                        $cliente_id = Kapsula::cadastarCliente($entrega, $comprador);
                        $response = Kapsula::realizarPedido($cliente_id, $plano->id_plano_trasnportadora);
                        $entrega->update($response);

                    }
                    if($plano->transportadora == 3) {
                        $response = LiftGold::realizarPedido($venda, $plano, $entrega, $comprador);
                        $entrega->update($response);
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

