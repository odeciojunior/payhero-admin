<?php

namespace Modules\PostBack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class PostBackController extends Controller {

    public function postBackListener(Request $request) {

        $dados = $request->all();

        Log::write('info', 'retorno do pagar.me : '. print_r($dados, true));

        if(isset($dados['event']) && $dados['event'] = 'transaction_status_changed'){

            $venda = Venda::find($dados['transaction']['metadata']['id_venda']);

            if($venda != null){

                $venda->update([
                    'status' => $dados['transaction']['status'],
                    'pagamento_id' => $dados['id']
                ]);
            }
            else{

                Log::write('info', 'VENDA NÃO ENCONTRADA!!!');
            }

        }

        return true;
    }

}

