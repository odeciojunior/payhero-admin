<?php

namespace Modules\Vitrine\Http\Controllers;

use App\User;
use App\Plano;
use App\Empresa;
use App\Projeto;
use App\Afiliado;
use App\UserProjeto;
use Illuminate\Http\Request;
use App\SolicitacaoAfiliacao;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;

class VitrineController extends Controller {

    public function index() {

        $afiliacoesUsuario = Afiliado::where('user',\Auth::user()->id)->pluck('projeto')->toArray();

        $projetosDisponiveis = UserProjeto::where([
            ['user','!=',\Auth::user()->id],
            ['tipo','produtor']
        ])->pluck('projeto')->toArray();

        $afiliacoesPendentes = SolicitacaoAfiliacao::where([
            ['user', \Auth::user()->id],
            ['status','Pendente']
        ])->pluck('projeto')->toArray();

        $projetos = Projeto::select('id','foto','nome','descricao','porcentagem_afiliados')
                            ->whereIn('id', $projetosDisponiveis)
                            ->whereNotIn('id',$afiliacoesUsuario)
                            ->whereNotIn('id',$afiliacoesPendentes)
                            ->where('visibilidade','publico')
                            ->get()->toArray();

        foreach($projetos as &$projeto){
            $projetoUsuario = UserProjeto::where([
                ['projeto',$projeto['id']],
                ['tipo','produtor']
            ])->first();
            $usuario = User::find($projetoUsuario['user']);
            $projeto['produtor'] = $usuario['name'];
            $plano = Plano::where('projeto',$projeto['id'])->max('preco');

            $maiorComissao = number_format($plano * 0.90, 2);

            $maiorComissao = str_replace(',','',$maiorComissao);

            $maiorComissao = number_format($maiorComissao * $projeto['porcentagem_afiliados'] / 100 ,2);

            $projeto['maior_comissao'] = str_replace('.',',',$maiorComissao);

            $projeto['id'] = Hashids::encode($projeto['id']);
        }

        return view('vitrine::index',[
            'projetos' => $projetos
        ]); 
    }

}


