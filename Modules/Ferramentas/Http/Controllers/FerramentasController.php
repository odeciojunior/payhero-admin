<?php

namespace Modules\Ferramentas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Notificacoes\Notifications\Teste;

class FerramentasController extends Controller {

    public function index() {

        // $response = \Ebanx\Ebanx::doQuery([
        //     'hash' => '5cbf6fe0b149d44bcbb4c6be6db37fd84da757cb2498492f'
        // ]);

        // dd($response);

        return view('ferramentas::index');
    }

}
