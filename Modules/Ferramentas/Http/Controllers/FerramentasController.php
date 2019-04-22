<?php

namespace Modules\Ferramentas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Notificacoes\Notifications\Teste;

class FerramentasController extends Controller {

    public function index() {

        // \Auth::user()->notify(new Teste());

        return view('ferramentas::index');
    }

}
