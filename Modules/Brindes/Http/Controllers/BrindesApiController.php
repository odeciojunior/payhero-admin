<?php

namespace Modules\Brindes\Http\Controllers;

use App\Brinde;
use App\Projeto;
use App\Entities\UserProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Brindes\Transformers\BrindesResource;

class BrindesApiController extends Controller {

    public function index(Request $request) {

        $projeto = Projeto::find(Hashids::decode($request->id_projeto));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $brindes = Brinde::where('projeto',Hashids::decode($projeto['id']));

        return BrindesResource::collection($brindes->paginate(10));
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

        Brinde::create($dados);

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

        $dados['projeto'] = $projeto['id'];

        $brinde = Brinde::select('titulo','descricao','tipo_brinde','link','created_at')
                        ->where('id',Hashids::decode($request->id_brinde))->first();

        if(!$brinde){
            return respose()->json('brinde não encontrado');
        }

        return response()->json($brinde);
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

        $brinde = Brinde::find(Hashids::decode($dados['id']));
        
        if(!$brinde){
            return response()->json('brinde não encontrado');
        }

        $brinde->update($dados);

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

        $brinde = Brinde::find(Hashids::decode($request->id_brinde));
        
        if(!$brinde){
            return response()->json('brinde não encontrado');
        }

        $brinde->delete();

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
