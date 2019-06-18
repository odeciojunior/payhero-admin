<?php

namespace Modules\Tools\Http\Controllers;

use Slince\Shopify\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Slince\Shopify\PublicAppCredential;
use Modules\Notificacoes\Notifications\Teste;

class ToolsController extends Controller {

    public function index() {

        return view('tools::index');
    }

}

