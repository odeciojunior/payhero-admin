<?php

namespace Modules\Projetos\Http\Controllers;

use App\Foto;
use App\User;
use App\Plano;
use App\Empresa;
use App\Projeto;
use App\UserProjeto;
use App\UsuarioEmpresa;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ProjetosController extends Controller{

    public function index() {

        $projetos = array();

        if(\Auth::user()->hasRole('administrador geral')){
            $projetos = Projeto::all();
        }
        else{
            $projetos_usuario = UserProjeto::where('user', \Auth::user()->id)->get()->toArray();
            if($projetos_usuario != null){
                foreach($projetos_usuario as $projeto_usuario){
                    $projetos[] = Projeto::find($projeto_usuario['projeto']);
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

        $imagem = $request->file('foto_projeto');

        if ($imagem != null) {
            $nome_foto = 'projeto_' . $projeto->id . '_.' . $imagem->getClientOriginalExtension();

            Storage::delete('public/upload/projeto/'.$nome_foto);

            $imagem->move(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO, $nome_foto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nome_foto);

            $img->crop($dados['foto_w'], $dados['foto_h'], $dados['foto_x1'], $dados['foto_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/projeto/'.$nome_foto);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nome_foto);

            $projeto->update([
                'foto' => $nome_foto
            ]);
        }

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

        $imagem = $request->file('foto_projeto');

        if ($imagem != null) {
            $nome_foto = 'projeto_' . $projeto->id . '_.' . $imagem->getClientOriginalExtension();

            Storage::delete('public/upload/projeto/'.$nome_foto);

            $imagem->move(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO, $nome_foto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nome_foto);

            $img->crop($dados['foto_w'], $dados['foto_h'], $dados['foto_x1'], $dados['foto_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/projeto/'.$nome_foto);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO . $nome_foto);

            $projeto->update([
                'foto' => $nome_foto
            ]);
        }

        return response()->json('sucesso');
    }

    public function deletarProjeto($id){

        $projeto = Projeto::find($id);

        $projeto->delete();

        return redirect()->route('projetos');

    }

    public function projeto($id){

        $projeto = projeto::find($id);
        $foto = '/'.CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto->foto;

        return view('projetos::projeto',[
            'projeto' => $projeto,
            'foto' => $foto
        ]);
    }

    public function getConfiguracoesProjeto($id){

        $projeto = Projeto::find($id);

        $empresas_usuario = UsuarioEmpresa::where('user', \Auth::user()->id)->get()->toArray();

        foreach($empresas_usuario as $empresa_usuario){
            $empresas[] = Empresa::find($empresa_usuario['empresa']);
        }

        $view = view('projetos::editar',[
            'projeto' => $projeto,
            'empresas' => $empresas
        ]);

        return response()->json($view->render());
    }

    public function getDadosProjeto($id){

        $projeto = Projeto::find($id); 

        $user_projeto = UserProjeto::where([
            ['projeto',$id],
            ['tipo','produtor']
        ])->first();

        $usuario = User::find($user_projeto['user']);
        $planos = Plano::where('projeto',$projeto['id'])->get()->toArray();

        foreach($planos as &$plano){
            $foto = Foto::where('plano',$plano['id'])->first();
            $plano['foto'] = $foto->caminho_imagem;
            $plano['lucro'] = number_format($plano['preco'] * $projeto['porcentagem_afiliados'] / 100, 2);
        }
        
        $view = view('projetos::detalhes',[
            'projeto' => $projeto,
            'planos' => $planos,
            'produtor' => $usuario['name']
        ]);

        return response()->json($view->render());
    }

}
