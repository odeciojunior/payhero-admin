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
    }

}
