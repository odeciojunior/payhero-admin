<?php

namespace Modules\Afiliados\Http\Controllers;

use App\Foto;
use App\User;
use App\Plano;
use App\Dominio;
use App\Empresa;
use App\Projeto;
use App\Afiliado;
use App\LinkAfiliado;
use App\UsuarioEmpresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class AfiliadosController extends Controller {

    public function afiliar($id_projeto) {

        $projeto = Projeto::find($id_projeto);

        $planos = Plano::where('projeto', $id_projeto)->get()->toArray();

        $afiliado = Afiliado::create([
            'user' => \Auth::user()->id,
            'projeto' => $projeto['id'],
            'porcentagem' => $projeto['porcentagem_afiliados']
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

        return view('afiliados::minhas_afiliacoes');
    }

    public function meusAfiliados(){

        return view('afiliados::meus_afiliados');
    }

    public function minhasAfiliacoes(){

        return view('afiliados::minhas_afiliacoes');
    }

    public function dadosMeusAfiliados(){

        $empresas_usuario = UsuarioEmpresa::where('user',\Auth::user()->id)->pluck('empresa')->toArray();

        $projetos = \DB::table('projetos as projeto')
            ->whereIn('projeto.empresa',$empresas_usuario)
            ->select([
                'projeto.id',
                'projeto.porcentagem_afiliados',
                'projeto.nome',
                'projeto.visibilidade',
        ]);

        return Datatables::of($projetos)
        ->addColumn('detalhes', function ($projeto) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_projeto' data-placement='top' data-toggle='tooltip' title='Detalhes' projeto='".$projeto->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                            Detalhes
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);

    }

    public function dadosMinhasAfiliacoes(){

        $empresas_usuario = UsuarioEmpresa::where('user',\Auth::user()->id)->pluck('empresa')->toArray();

        $projetos_usuario = Projeto::whereIn('empresa',$empresas_usuario)->pluck('id')->toArray();

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

    public function getAfiliadosProjeto($id_projeto){

        $projeto = Projeto::find($id_projeto);

        $empresas_usuario = UsuarioEmpresa::where('empresa',$projeto['empresa'])->first();

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

        $empresas = [];
        $empresas_usuario = UsuarioEmpresa::where('user',\Auth::user()->id)->get()->toArray();
        foreach($empresas_usuario as $empresa_usuario){
            $empresas[] = Empresa::find($empresa_usuario['empresa']);
        }

        $empresas_usuario = UsuarioEmpresa::where('empresa',$projeto['empresa'])->first();
        $usuario = User::find($empresas_usuario['user']);
        $planos = Plano::where('projeto',$projeto['id'])->get()->toArray();

        foreach($planos as &$plano){
            $foto = Foto::where('plano',$plano['id'])->first();
            $plano['foto'] = $foto->caminho_imagem;
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
