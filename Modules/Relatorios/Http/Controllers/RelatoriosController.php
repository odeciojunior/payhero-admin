<?php

namespace Modules\Relatorios\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class RelatoriosController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function vendas()
    {
        return view('relatorios::index');
    }

}
