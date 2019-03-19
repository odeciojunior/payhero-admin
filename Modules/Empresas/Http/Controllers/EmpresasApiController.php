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

    public function create(Request $request){

        $dados = $request->all();

        Empresa::create($dados);

        return response()->json("sucesso");
    }

    public function show($id){

        $empresa = Empresa::find($id);

        return response()->json($empresa);
    }

    public function update(Request $request){

        $dados = $request->all();

        if(!isset($dados['id'])){
            return response()->json('id não informado');
        }

        $empresa = Empresa::find($dados['id']);

        if(!$empresa){
            return response()->json('empresa não encontrada');
        }

        $empresa->update($dados);

        return response()->json('sucesso');
    }

    public function delete($id){

        $empresa = Empresa::find($id);

        if(!$empresa){
            return response()->json('empresa não encontrada');
        }

        $empresa->delete();

        return response()->json('sucesso');
    }

}
