<?php

namespace Modules\Brindes\Http\Controllers;

use App\Brinde;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Brindes\Transformers\BrindesResource;

class BrindesApiController extends Controller {

    public function index(Request $request) {

        $brindes = Brinde::where('projeto',Hashids::decode($request->id_projeto));

        return BrindesResource::collection($brindes->paginate(10));
    }

    public function store(Request $request) {

        $dados = $request->all();
        $dados['projeto'] = Hashids::decode($request->id_projeto);

        Brinde::create($dados);

        return response()->json('sucesso');
    }

    public function show(Request $request) {

        $brinde = Brinde::select('titulo','descricao','tipo_brinde','link','created_at')
                        ->where('id',Hashids::decode($request->id_brinde))->first();

        return response()->json($brinde);
    }

    public function update(Request $request) {

        $dados = $request->all();

        Brinde::find(Hashids::decode($dados['id']))->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        Brinde::find(Hashids::decode($request->id_brinde))->delete();

        return response()->json('sucesso');
    }


}
