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
            return response()->json('sucesso');
        }

        return response()->json($projeto);
    }

    public function update(Request $request)  {

        $projeto = Projeto::find(Hashids::decode($dados['id']));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        $projeto->update($dados);

        return response()->json('sucesso');
    }

    public function destroy($id)  {

        $projeto = Projeto::find(Hashids::decode($id));

        if(!$projeto){
            return response()->json('projeto não encontrado');
        }

        $projeto->delete();

        return response()->json('sucesso');
    }

}
