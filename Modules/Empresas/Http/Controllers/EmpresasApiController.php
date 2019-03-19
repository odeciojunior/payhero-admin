<?php

namespace Modules\Empresas\Http\Controllers;

use App\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Empresas\Transformers\EmpresasResource;

class EmpresasApiController extends Controller {


    public function index()  {

        $empresas = Empresa::where('user',\Auth::user()->id);

        return EmpresasResource::collection($empresas->paginate());
    }

    public function show($id){

        $empresa = Empresa::find($id);

        return response()->json($empresa);
    }
}
