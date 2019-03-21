<?php

namespace Modules\CuponsDesconto\Http\Controllers;

use App\Cupom;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CuponsDescontoApiController extends Controller {

    public function index(Request $request) {

        $cupons_desconto = Cupom::select('id','nome','tipo','valor','cod_cupom','status')->where('projeto',$request->id_projeto);

        return response()->json($cupons_desconto->paginate());
    }

    public function store(Request $request) {

        $dados = $request->all();
        $dados['projeto'] = $request->id_projeto;

        Cupom::create($dados);

        return response()->json('sucesso');
    }

    public function show(Request $request) {

        $cupom_desconto = Cupom::select('id','nome','tipo','valor','cod_cupom','status','created_at')->where('id',$request->id_cupom)->first();

        return response()->json($cupom_desconto);
    }

    public function update(Request $request) {

        $dados = $request->all();

        Cupom::find($dados['id'])->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        Cupom::find($request->id_cupom)->delete();

        return response()->json('sucesso');
    }

}
