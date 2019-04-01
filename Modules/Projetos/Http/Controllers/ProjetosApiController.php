<?php

namespace Modules\Projetos\Http\Controllers;

use App\Projeto;
use App\UserProjeto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Projetos\Transformers\ProjetosResource;

class ProjetosApiController extends Controller {

    public function index()  {

        $projetos_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor']
        ])->pluck('projeto')->toArray();

        $projetos = Projeto::whereIn('id',$projetos_usuario);

        return ProjetosResource::collection($projetos_usuario->paginate());
    }

    public function store(Request $request)  {

        $dados = $request->all();

        $projeto = Projeto::create($dados);

        UserProjeto::create([
            'user'              => \Auth::user()->id,
            'projeto'           => $projeto->id,
            'empresa'           => $dados['empresa'],
            'tipo'              => 'produtor',
            'responsavel_frete' => true,
            'permissao_acesso'  => true,
            'permissao_editar'  => true,
            'status'            => 'ativo'
        ]);

        return response()->json('sucesso');
    }

    public function show($id)  {

        $projeto = Projeto::find(Hashids::decode($id));

        if(!$projeto){
            return response()->json('Projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $projeto_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor'],
            ['projeto', $projeto['id']]
        ])->first();

        if(!$projeto_usuario){
            return response()->json('Sem autorização');
        }

        return response()->json($projeto);
    }

    public function update(Request $request)  {

        $projeto = Projeto::find(Hashids::decode($dados['id']));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        if(!$this->isAuthorized($projeto['id'])){
            return response()->json('não autorizado');
        }

        $projeto_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor'],
            ['projeto', $projeto['id']]
        ])->first();

        if(!$projeto_usuario){
            return response()->json('Sem autorização');
        }

        $projeto->update($dados);

        return response()->json('sucesso');
    }

    public function destroy($id)  {

        $projeto = Projeto::find(Hashids::decode($id));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        $projeto_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor'],
            ['projeto', $projeto['id']]
        ])->first();

        if(!$projeto_usuario){
            return response()->json('Sem autorização');
        }

        $projeto->delete();

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
