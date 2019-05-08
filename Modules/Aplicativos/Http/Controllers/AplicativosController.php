<?php

namespace Modules\Aplicativos\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AplicativosController extends Controller {

    public function index() {

        return view('aplicativos::index');
    }
}
