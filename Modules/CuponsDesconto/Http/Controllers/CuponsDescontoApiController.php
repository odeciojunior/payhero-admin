<?php

namespace Modules\CuponsDesconto\Http\Controllers;

use App\Cupom;
use App\Projeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Modules\CuponsDesconto\Transformers\CuponsDescontoResource;

class CuponsDescontoApiController extends Controller {

    public function index(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $cupons_desconto = Cupom::select('id','nome','tipo','valor','cod_cupom','status')
                                ->where('projeto',Hashids::decode($request->id_projeto));

        return CuponsDescontoResource::collection($cupons_desconto->paginate(10));
    }

    public function store(Request $request) {

        $dados = $request->all();

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $dados['projeto'] = $projeto['id'];

        Cupom::create($dados);

        return response()->json('sucesso');
    }

    public function show(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $cupom_desconto = Cupom::select('id','nome','tipo','valor','cod_cupom','status','created_at')
                        ->where('id',Hashids::decode($request->id_cupom))->first();

        return response()->json($cupom_desconto);
    }

    public function update(Request $request) {

        $dados = $request->all();

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }
        
        Cupom::find(Hashids::decode($dados['id']))->update($dados);

        return response()->json('sucesso');
    }

    public function destroy(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        Cupom::find(Hashids::decode($request->id_cupom))->delete();

        return response()->json('sucesso');
    }

    public function isAuthorized($id_projeto){

        $projeto_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor'],
            ['projeto', $id_projeto]
        ])->first();

        if(!$projeto_usuario){
            return false;
        }

        return true;
    }

}
