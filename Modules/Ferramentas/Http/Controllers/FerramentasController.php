<?php

namespace Modules\Ferramentas\Http\Controllers;

use Slince\Shopify\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Slince\Shopify\PublicAppCredential;
use Modules\Notificacoes\Notifications\Teste;

class FerramentasController extends Controller {

    public function index() {

        return view('ferramentas::index');
    }

}

