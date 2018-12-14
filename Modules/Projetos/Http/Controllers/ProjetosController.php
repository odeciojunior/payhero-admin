<?php

namespace Modules\Projetos\Http\Controllers;

use App\Empresa;
use App\Projeto;
use App\UsuarioEmpresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ProjetosController extends Controller{

    public function index() {

        $projetos = array();

        if(\Auth::user()->hasRole('administrador geral')){
            $projetos = Projeto::all();
        }
        else{
            $empresas_usuario = UsuarioEmpresa::where('user', \Auth::user()->id)->get()->toArray();
            if($empresas_usuario != null){
                foreach($empresas_usuario as $empresa_usuario){
                    $projetos_empresa = Projeto::where('empresa',$empresa_usuario['empresa'])->get()->toArray();
                    if($projetos_empresa != null){
                        foreach($projetos_empresa as $projeto){
                            $projetos[] = $projeto;
                        }
                    }
                }
            }
        }

        return view('projetos::index',[
            'projetos' => $projetos
        ]); 
    }

    public function cadastro() {

        $empresas = array();

        $empresas_usuario = UsuarioEmpresa::where('user', \Auth::user()->id)->get()->toArray();

        foreach($empresas_usuario as $empresa_usuario){
            $empresas[] = Empresa::find($empresa_usuario['empresa']);
        }

        return view('projetos::cadastro',[
            'empresas' => $empresas
        ]);

    }

    public function cadastrarProjeto(Request $request){

        $dados = $request->all();

        $projeto = Projeto::create($dados);

        $imagem = $request->file('imagem');

        if ($imagem != null) {
            $nome_imagem = 'projeto_' . $projeto->id . '_.' . $imagem->getClientOriginalExtension();

            $imagem->move(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO, $nome_imagem);

            $projeto->update([
                'foto' => $nome_imagem
            ]);
        }

        return redirect()->route('projetos');
    }

    public function editarProjeto($id){

        $projeto = Projeto::find($id);

        $empresas_usuario = UsuarioEmpresa::where('user', \Auth::user()->id)->get()->toArray();

        foreach($empresas_usuario as $empresa_usuario){
            $empresas[] = Empresa::find($empresa_usuario['empresa']);
        }

        return view('projetos::editar',[
            'projeto' => $projeto,
            'empresas' => $empresas
        ]);

    }

    public function updateProjeto(Request $request){

        $dados = $request->all();

        $projeto = Projeto::find($dados['id']);
        $projeto->update($dados);

        $imagem = $request->file('imagem');

        if ($imagem != null) {
            $nome_imagem = 'projeto_' . $projeto->id . '_.' . $imagem->getClientOriginalExtension();

            $imagem->move(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO, $nome_imagem);

            $projeto->update([
                'foto' => $nome_imagem
            ]);
        }


        return redirect()->route('projetos');
    }

    public function deletarProjeto($id){

        $projeto = Projeto::find($id);

        $projeto->delete();

        return redirect()->route('projetos');

    }

    public function dadosprojeto() {

        $projetos = \DB::table('projetos as projeto')
            ->get([
                'projeto.id',
                'projeto.nome',
                'projeto.descricao',
        ]);

        return Datatables::of($projetos)
        ->addColumn('detalhes', function ($projeto) {
            return "<span>
                        <a href='/projetos/projeto/".$projeto->id."' class='btn btn-outline btn-success' data-placement='top' data-toggle='tooltip' title='selecionar'>
                            <i class='icon wb-check' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/projetos/editar/$projeto->id' class='btn btn-outline btn-primary editar_projeto' data-placement='top' data-toggle='tooltip' title='Editar' projeto='".$projeto->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_projeto' data-placement='top' data-toggle='tooltip' title='Excluir' projeto='".$projeto->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function projeto($id){

        $projeto = projeto::find($id);
        $foto = '/'.CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto->foto;

        return view('projetos::projeto',[
            'projeto' => $projeto,
            'foto' => $foto
        ]);
    }


}
