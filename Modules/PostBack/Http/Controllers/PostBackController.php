<?php

namespace Modules\PostBack\Http\Controllers;

use App\User;
use App\Plano;
use App\Venda;
use App\Entrega;
use Carbon\Carbon;
use App\Comprador;
use App\Transacao;
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

class PostBackController extends Controller {

    public function postBackListener(Request $request){

        $dados = $request->all();

        Log::write('info', 'Notificação do Ebanx : '. print_r($dados, true));

        $response = \Ebanx\Ebanx::doQuery([
            'hash' => $dados['hash_codes']
        ]);

        $venda = Venda::where('pagamento_id',$dados['hash_codes'])->first();

        if(!$venda){
            Log::write('info', 'Venda não encontrada');
            return 'success';
        }

        if($response->payment->status != $venda->pagamento_status){

            $venda->update([
                'pagamento_status' => $response->payment->status
            ]);

            $transacoes = Transacao::where('venda',$venda['id'])->get()->toArray();

            if($response->payment->status == 'CA'){

                foreach($transacoes as $transacao){
                    Transacao::find($transacao['id'])->update('status','cancelada');
                }
            }

            else if($response->payment->status == 'CO'){

                date_default_timezone_set('America/Sao_Paulo');

                $venda->update([
                    'data_finalizada' => \Carbon\Carbon::now()
                ]);

                foreach($transacoes as $t){

                    $transacao = Transacao::find($t['id']);

                    if($transacao['emrpesa'] != null){

                        $transacao->update([
                            'status'         => 'pago',
                            'data_liberacao' => Carbon::now()->addDays(30)->format('Y-m-d')
                        ]);
                    }
                }

                if($venda['pedido_shopify'] != ''){

                    $planosVenda = PlanoVenda::where('venda', $venda['id'])->first();

                    $plano = Plano::find($planosVenda->plano);

                    $integracaoShopify = IntegracaoShopify::where('projeto',$plano['projeto'])->first();

                    try{
                        $credential = new PublicAppCredential($integracaoShopify['token']);

                        $client = new Client($credential, $integracaoShopify['url_loja'], [
                            'metaCacheDir' => './tmp'
                        ]);

                        $transaction = $client->getTransactionManager()->create($venda['pedido_shopify'],[
                            "kind"      => "capture",
                        ]);

                    }
                    catch(\Exception $e){
                        Log::write('info', 'erro ao alterar estado do pedido no shopify com a venda '.$venda['id']);
                        Log::write('info',  print_r($e, true) );
                    }

                }
            }

        }

        return 'success';
    }

}
