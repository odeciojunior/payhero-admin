<?php

namespace Modules\Afiliados\Http\Controllers;

use App\Foto;
use App\User;
use App\Plano;
use App\Dominio;
use App\Empresa;
use App\Projeto;
use App\Afiliado;
use Carbon\Carbon;
use App\UserProjeto;
use App\LinkAfiliado;
use Illuminate\Http\Request;
use App\SolicitacaoAfiliacao;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class AfiliadosController extends Controller {

    public function afiliar($id_projeto) {

        $projeto = Projeto::find($id_projeto);

        if(!$projeto['afiliacao_automatica']){

            SolicitacaoAfiliacao::create([
                'user'      => \Auth::user()->id,
                'projeto'   => $projeto['id'],
                'status'    => 'Pendente'
            ]);

            \Session::flash('success', "Solicitação de afiliação enviada para o produtor do projeto!");
            return view('afiliados::minhas_afiliacoes');
        }

        $planos = Plano::where('projeto', $id_projeto)->get()->toArray();

        $empresa = Empresa::where([
            ['user', \Auth::user()->id],
            ['recipient_id','!=','']
        ])->first();

        $afiliado = Afiliado::create([
            'user' => \Auth::user()->id,
            'projeto' => $projeto['id'],
            'porcentagem' => $projeto['porcentagem_afiliados'],
            'empresa'  => @$empresa->id
        ]);

        LinkAfiliado::create([
            'afiliado' => $afiliado->id,
            'parametro' => $this->randString(10)
        ]);

        foreach($planos as $plano){
            LinkAfiliado::create([
                'afiliado' => $afiliado->id,
                'parametro' => $this->randString(10),
                'plano' => $plano['id']
            ]);
        }

        \Session::flash('success', "Afiliação realizada com sucesso!");
        return view('afiliados::minhas_afiliacoes');
    }

    public function confirmarAfiliacao(Request $request) {

        $dados = $request->all();

        $solicitacao_afiliacao = SolicitacaoAfiliacao::find($dados['id']);

        $projeto = Projeto::find($solicitacao_afiliacao['projeto']);

        $planos = Plano::where('projeto', $solicitacao_afiliacao['projeto'])->get()->toArray();

        $empresa = Empresa::where([
            ['user', $solicitacao_afiliacao['user']],
            ['recipient_id','!=','']
        ])->first();

        $afiliado = Afiliado::create([
            'user' => $solicitacao_afiliacao['user'],
            'projeto' => $projeto['id'],
            'porcentagem' => $projeto['porcentagem_afiliados'],
            'empresa'  => @$empresa->id
        ]);

        LinkAfiliado::create([
            'afiliado' => $afiliado->id,
            'parametro' => $this->randString(10)
        ]);

        foreach($planos as $plano){
            LinkAfiliado::create([
                'afiliado' => $afiliado->id,
                'parametro' => $this->randString(10),
                'plano' => $plano['id']
            ]);
        }

        $solicitacao_afiliacao->update([
            'status' => 'Confirmada'
        ]);

        return response()->json('Sucesso');
    }

    public function meusAfiliados(){

        return view('afiliados::meus_afiliados');
    }

    public function minhasAfiliacoes(){

        return view('afiliados::minhas_afiliacoes');
    }

    public function dadosMeusAfiliados(){

        $projetos_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor']
        ])->pluck('projeto')->toArray();

        $usuarios_afiliados = \DB::table('afiliados as afiliado')
            ->leftJoin('users as user','afiliado.user','user.id')
            ->leftJoin('projetos as projeto','afiliado.projeto','projeto.id')
            ->whereIn('projeto.id',$projetos_usuario)
            ->select([
                'afiliado.id',
                'user.name',
                'afiliado.porcentagem',
                'projeto.nome',
        ]);

        return Datatables::of($usuarios_afiliados)
        ->addColumn('detalhes', function ($afiliado) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_afiliado' data-placement='top' data-toggle='tooltip' title='Detalhes' afiliado='".$afiliado->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Detalhes
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function dadosAfiliacoesPendentes(){

        $projetos_usuario = UserProjeto::where([
            ['user',\Auth::user()->id],
            ['tipo','produtor']   
        ])->pluck('projeto')->toArray();

        $solicitacoes_afiliacoes = \DB::table('solicitacoes_afiliacoes as solicitacao_afiliacao')
            ->leftJoin('users as user','solicitacao_afiliacao.user','user.id')
            ->leftJoin('projetos as projeto','solicitacao_afiliacao.projeto','projeto.id')
            ->whereIn('solicitacao_afiliacao.projeto',$projetos_usuario)
            ->where('solicitacao_afiliacao.status','Pendente')
            ->select([
                'solicitacao_afiliacao.id',
                'user.name',
                'projeto.porcentagem_afiliados',
                'solicitacao_afiliacao.created_at as data_solicitacao',
                'projeto.nome',
        ]);

        return Datatables::of($solicitacoes_afiliacoes)
        ->editColumn('data_solicitacao', function($afiliado){
            return Carbon::parse($afiliado->data_solicitacao)->format('d/m/Y H:i');
        })
        ->addColumn('detalhes', function ($solicitacao_afiliacao) {
            return "<span>
                        <a class='btn btn-outline btn-success confirmar_afiliacao' data-placement='top' data-toggle='tooltip' title='Confirmar' solicitacao_afiliacao='".$solicitacao_afiliacao->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Confirmar afiliação
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function dadosMinhasAfiliacoes(){

        $empresas_usuario = Empresa::where('user',\Auth::user()->id)->pluck('id')->toArray();

        $projetos_usuario = UserProjeto::where('user',\Auth::user()->id)->pluck('id')->toArray();

        $afiliados = \DB::table('afiliados as afiliado')
            ->leftJoin('projetos as projeto','projeto.id','=','afiliado.projeto')
            ->where('afiliado.user',\Auth::user()->id)
            ->select([
                'afiliado.id',
                'afiliado.porcentagem',
                'afiliado.projeto',
                'projeto.nome',
                'afiliado.created_at as data_afiliacao',
        ]);

        return Datatables::of($afiliados)
        ->editColumn('data_afiliacao', function($afiliado){
            return Carbon::parse($afiliado->data_afiliacao)->format('d/m/Y H:i');
        })
        ->addColumn('detalhes', function ($afiliado) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_afiliacao' data-placement='top' data-toggle='tooltip' title='Detalhes' afiliado='".$afiliado->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Detalhes
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function dadosMinhasAfiliacoesPendentes(){

        $empresas_usuario = Empresa::where('user',\Auth::user()->id)->pluck('id')->toArray();

        $projetos_usuario = UserProjeto::where('user',\Auth::user()->id)->pluck('id')->toArray();

        $solicitacoes_afiliacoes = \DB::table('solicitacoes_afiliacoes as solicitacao_afiliacao')
            ->leftJoin('projetos as projeto','projeto.id','=','solicitacao_afiliacao.projeto')
            ->where('solicitacao_afiliacao.user',\Auth::user()->id)
            ->where('solicitacao_afiliacao.status','Pendente')
            ->select([
                'solicitacao_afiliacao.id',
                'solicitacao_afiliacao.projeto',
                'solicitacao_afiliacao.status',
                'projeto.nome',
                'solicitacao_afiliacao.created_at as data_solicitacao',
        ]);

        return Datatables::of($solicitacoes_afiliacoes)
        ->editColumn('data_solicitacao', function($solicitacao_afiliacao){
            return Carbon::parse($solicitacao_afiliacao->data_solicitacao)->format('d/m/Y H:i');
        })
        ->make(true);

    }

    public function getAfiliadosProjeto($id_projeto){

        $projeto = Projeto::find($id_projeto);

        $afiliados = Afiliado::where('projeto',$id_projeto)->get()->toArray();

        foreach($afiliados as &$afiliado){
            $usuario = User::find($afiliado['user']);
            $afiliado['nome'] = $usuario['name'];
        }

        $view = view('afiliados::afiliados_projeto',[
            'projeto' => $projeto,
            'afiliados' => $afiliados
        ]);

        return response()->json($view->render());

    }

    public function getDetalhesAfiliacao($id_afiliado){

        $afiliado = Afiliado::find($id_afiliado);

        $projeto = Projeto::find($afiliado['projeto']);

        $dominio = Dominio::where('projeto',$afiliado['projeto'])->first();

        $set_coockie_url = "checkout.".$dominio['dominio']."/"."setcookie/";

        $url_pagina = $set_coockie_url.LinkAfiliado::where([
            ['afiliado', $id_afiliado],
            ['plano' , null]
        ])->first()['parametro'];

        $empresas = Empresa::where('user',\Auth::user()->id)->get()->toArray();

        $projeto_usuario = UserProjeto::where([
            ['projeto',$projeto['id']],
            ['tipo','produtor']
        ])->first();
        $usuario = User::find($projeto_usuario['user']);
        $planos = Plano::where('projeto',$projeto['id'])->get()->toArray();

        foreach($planos as &$plano){
            $plano['lucro'] = number_format($plano['preco'] * $projeto['porcentagem_afiliados'] / 100, 2);
            $plano['url'] = $set_coockie_url.LinkAfiliado::where([
                ['afiliado', $id_afiliado],
                ['plano' , $plano['id']]
            ])->first()['parametro'];
        }

        $view = view('afiliados::detalhes_afiliacao',[
            'projeto' => $projeto,
            'planos' => $planos,
            'produtor' => $usuario['name'],
            'url_pagina' => $url_pagina,
            'empresas' => $empresas,
            'afiliado' => $afiliado
        ]);

        return response()->json($view->render());

    }

    public function setEmpresaAfiliacao(Request $request){

        $dados = $request->all();

        Afiliado::find($dados['afiliado'])->update($dados);

        return response()->json('sucesso');
    }

    function randString($size){

        $novo_parametro = false;

        while(!$novo_parametro){

            $basic = 'abcdefghijlmnopqrstuvwxyz0123456789';

            $parametro = "";

            for($count= 0; $size > $count; $count++){
                $parametro.= $basic[rand(0, strlen($basic) - 1)];
            }

            $novo_link = LinkAfiliado::where('parametro', $parametro)->first();

            if($novo_link == null){
                $novo_parametro = true;
            }

        }

        return $parametro;
    }

}
