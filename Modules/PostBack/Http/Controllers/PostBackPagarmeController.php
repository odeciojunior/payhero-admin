<?php

namespace Modules\PostBack\Http\Controllers;

use App\User;
use App\Plano;
use App\Venda;
use App\Empresa;
use App\Entrega;
use App\Comprador;
use App\Transacao;
use Carbon\Carbon;
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

class PostBackPagarmeController extends Controller {

    public function postBackListener(Request $request) {

        $dados = $request->all();

        Log::write('info', 'retorno do pagar.me : '. print_r($dados, true));
 
        if(isset($dados['event']) && $dados['event'] = 'transaction_status_changed'){

            $venda = Venda::find($dados['transaction']['metadata']['id_venda']);

            Log::write('info', 'alterando dados da venda : '. $venda['id']);

            if($venda == null){
                Log::write('info', 'VENDA NÃO ENCONTRADA!!!');
                return 'sucesso';
            }

            if($dados['transaction']['status'] == $venda['pagamento_status']){
                return 'sucesso';
            }

            $transacoes = Transacao::where('venda',$venda->id)->get()->toArray();

            if($dados['transaction']['status'] == 'paid'){

                date_default_timezone_set('America/Sao_Paulo');

                $venda->update([
                    'data_finalizada' => Carbon::now(),
                    'status'          => 'paid',
                ]);

                foreach($transacoes as $t){

                    $transacao = Transacao::find($t['id']);

                    if($transacao['empresa'] != null){

                        $empresa = Empresa::find($transacao['empresa']);

                        $user = User::find($empresa['user']);

                        $transacao->update([
                            'status'         => 'pago',
                            'data_liberacao' => Carbon::now()->addDays($user['dias_antecipacao'])->format('Y-m-d')
                        ]);
                    }
                    else{
                        $transacao->update([
                            'status' => 'pago',
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
            else{
                foreach($transacoes as $transacao){
                    Transacao::find($transacao['id'])->update('status',$dados['transaction']['status']);
                }
            }
        }
        return 'sucesso';
    }

}

