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

            $venda = Venda::find($dados['transaction']['metadata']['id_venda']);

            if($venda != null){

                ($dados['transaction']['status'] == 'paid') ? $data = date('Y-m-d H:i:s') : $data = '';

                $venda->update([
                    'pagamento_status' => $dados['transaction']['status'],
                    'pagamento_id' => $dados['id'],
                    'data_finalizada' => $data
                ]);
            }
            else{

                Log::write('info', 'VENDA NÃO ENCONTRADA!!!');
            }

        }

        return 'true';
    }

}

