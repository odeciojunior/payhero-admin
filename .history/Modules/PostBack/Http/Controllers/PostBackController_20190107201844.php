<?php

namespace Modules\PostBack\Http\Controllers;

use App\Venda;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class PostBackController extends Controller {

    public function postBackListener(Request $request) {

        $dados = $request->all();

        Log::write('info', 'retorno do pagar.me : '. print_r($dados, true));

        if(isset($dados['event']) && $dados['event'] = 'transaction_status_changed'){

            Log::write('info', 'entrou no if ');

            $venda = Venda::find($dados['transaction']['metadata']['id_venda']);

            if($venda != null){

                if($dados['transaction']['status'] == 'paid' && $venda['pagamento_status'] != 'paid'){
                    date_default_timezone_set('America/Sao_Paulo');
                    $venda->update([
                        'pagamento_status' => $dados['transaction']['status'],
                        'pagamento_id' => $dados['id'],
                        'data_finalizada' => date('Y-m-d H:i:s', time())
                    ]);
                }
                else{
                    $venda->update([
                        'pagamento_status' => $dados['transaction']['status'],
                        'pagamento_id' => $dados['id'],
                    ]);
                }
            }
            else{

                Log::write('info', 'VENDA NÃO ENCONTRADA!!!');
            }

        }

        return 'sucesso';
    }

}

