<?php

namespace Modules\Transferencias\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class TransferenciasController extends Controller {


    public function index() {

        return view('transferencias::index');
    }

}
