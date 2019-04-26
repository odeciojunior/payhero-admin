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

        // $cert = file_get_contents('ebanx-notifications-public.pem');
        // $data = file_get_contents("php://input");
        // $signature = base64_decode($_SERVER['HTTP_X_SIGNATURE_CONTENT']);
        
        // // http://php.net/manual/en/function.openssl-verify.php
        // $result = openssl_verify($data, $signature, $cert);
        
        // if ($result === 1)
        // {
        //     echo "OK, signature is correct.";
        // }
        // else
        // {
        //     echo "ERROR, the signature is incorrect.";
        // }

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

                foreach($transacoes as $transacao){

                    Transacao::find($transacao['id'])->update([
                        ['status','pago'],
                        ['data_liberacao' => Carbon::now()->addDays(30)->format('Y-m-d')]
                    ]);
                }
            }

        }

        return 'success';
    }

}

