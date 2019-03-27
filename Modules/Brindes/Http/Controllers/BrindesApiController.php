<?php

namespace Modules\Brindes\Http\Controllers;

use App\Brinde;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
 
class BrindesApiController extends Controller {

    public function index(Request $request) {

        $brindes = Brinde::select('id','titulo','descricao','tipo_brinde')->where('projeto',$request->id_projeto);

        return response()->json($brindes->paginate());
    }

    public function store(Request $request) {

        $dados = $request->all();
        $dados['projeto'] = $request->id_projeto;

        Brinde::create($dados);

        return response()->json('sucesso');
    }

    public function show(Request $request) {

        $brinde = Brinde::select('titulo','descricao','tipo_brinde','link','created_at')->where('id',$request->id_brinde)->first();

        return response()->json($brinde);
    }

    public function update(Request $request) {

        $dados = $request->all();

        Brinde::find($dados['id'])->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        Brinde::find($request->id_brinde)->delete();

        return response()->json('sucesso');
    }


}
