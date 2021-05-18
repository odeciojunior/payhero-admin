<?php

namespace Modules\Showcase\Http\Controllers;

use App\Empresa;
use App\Projeto;
use App\Afiliado;
use Illuminate\Http\Request;
use App\SolicitacaoAfiliacao;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;

class ShowcaseApiController extends Controller {

    public function index() {

        $afiliacoes_usuario = Afiliado::where('user',\Auth::user()->id)->pluck('projeto')->toArray();

        $projetos_disponiveis = UserProjeto::where([
            ['user','!=',\Auth::user()->id],
            ['tipo','produtor']
        ])->pluck('projeto')->toArray();

        $afiliacoes_pendentes = SolicitacaoAfiliacao::where([
            ['user', 6],
            ['status','Pendente']
        ])->pluck('projeto')->toArray();

        $projetos = Projeto::select('id','foto','nome','descricao')
                            ->whereIn('id', $projetos_disponiveis)
                            ->whereNotIn('id',$afiliacoes_usuario)
                            ->whereNotIn('id',$afiliacoes_pendentes)
                            ->where('visibilidade','publico')
                            ->get()->toArray();

        foreach($projetos as &$projeto){
            $projeto['id'] = Hashids::encode($projeto['id']);
            $projeto_usuario = UserProjeto::where([
                ['projeto',$projeto['id']],
                ['tipo','produtor']
            ])->first();
            $usuario = User::find($projeto_usuario['user']);
            $projeto['produtor'] = $usuario['name'];
        }

        return response()->json([
            'projetos' => $projetos
        ]);
    }

}
