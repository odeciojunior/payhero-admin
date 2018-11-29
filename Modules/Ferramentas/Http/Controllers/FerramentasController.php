<?php

namespace Modules\Ferramentas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class FerramentasController extends Controller {

    public function index() {

        return view('ferramentas::index');
    }

}
