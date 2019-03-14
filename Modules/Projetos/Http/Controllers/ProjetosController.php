<?php

namespace Modules\Projetos\Http\Controllers;

use App\Foto;
use App\User;
use App\Plano;
use App\Empresa;
use App\Projeto;
use App\UserProjeto;
use App\MaterialExtra;
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
                    $projeto = Projeto::find($projeto_usuario['projeto']);
                    if($projeto){
                        $projetos[] = $projeto;
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

        $empresas = Empresa::where('user', \Auth::user()->id)->get()->toArray();

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

        $empresas = Empresa::where('user', \Auth::user()->id)->get()->toArray();

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

    public function deletarProjeto(Request $request){

        $dados = $request->all();

        $projeto = Projeto::find($dados['projeto']);

        $projeto->delete();

        return response()->json('sucesso');

    }

    public function projeto($id){

        $projeto = projeto::find($id);
        $foto = '/'.CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto->foto."?dummy=".uniqid();

        return view('projetos::projeto',[
            'projeto' => $projeto,
            'foto' => $foto
        ]);
    }

    public function getConfiguracoesProjeto($id){

        $projeto = Projeto::find($id);

        $materiais_extras = MaterialExtra::where('projeto',$projeto['id'])->get()->toArray();

        $empresas = Empresa::where('user', \Auth::user()->id)->get()->toArray();

        $view = view('projetos::editar',[
            'projeto' => $projeto,
            'empresas' => $empresas,
            'materiais_extras' => $materiais_extras
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
            $plano['lucro'] = number_format($plano['preco'] * $projeto['porcentagem_afiliados'] / 100, 2);
        }
        
        $view = view('projetos::detalhes',[
            'projeto' => $projeto,
            'planos' => $planos,
            'produtor' => $usuario['name']
        ]);

        return response()->json($view->render());
    }

    public function addMaterialExtra(Request $request){

        $dados = $request->all();

        $dados['descricao'] = $dados['descricao_material_extra'];

        if($dados['tipo'] == 'video'){
            $dados['material'] = $dados['material_extra_video'];
            MaterialExtra::create($dados);
        }
        else if($dados['tipo'] == 'imagem'){

            $material_extra = MaterialExtra::create($dados);

            $imagem = $request->file('material_extra_imagem');

            if ($imagem != null) {
                $nome_foto = 'foto_' . $material_extra->id . '_.' . $imagem->getClientOriginalExtension();
    
                Storage::delete('public/upload/materialextra/fotos/'.$nome_foto);
    
                $imagem->move(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJETO_FOTO, $nome_foto);
    
                $img = Image::make(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJETO_FOTO . $nome_foto);

                Storage::delete('public/upload/materialextra/fotos/'.$nome_foto);

                $img->save(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJETO_FOTO . $nome_foto);

                $material_extra->update([
                    'material' => $nome_foto
                ]);
            }

        }
        else if($dados['tipo'] == 'pdf'){

            $material_extra = MaterialExtra::create($dados);

            $arquivo = $request->file('material_extra_pdf');

            if ($arquivo != null) {
                $nome_pdf = 'pdf_' . $material_extra->id . '_.' . $arquivo->getClientOriginalExtension();

                Storage::delete('public/upload/materialextra/pdfs/'.$nome_pdf);

                $arquivo->move(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJETO_FOTO, $nome_pdf);

                $img = Image::make(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJETO_FOTO . $nome_pdf);

                Storage::delete('public/upload/materialextra/pdfs/'.$nome_pdf);

                $img->save(CaminhoArquivosHelper::CAMINHO_MATERIAL_EXTRA_PROJETO_FOTO . $nome_pdf);

                $material_extra->update([
                    'material' => $nome_pdf
                ]);
            }

        }

        return response()->json('sucesso');
    }

    public function deletarMaterialExtra(Request $request){

        $dados = $request->all();

        MaterialExtra::find($dados['id_material_extra'])->delete();

        return response()->json('sucesso');
    }

    public function getProjetos(){

        $projetos_usuario = UserProjeto::where('user', \Auth::user()->id)->get()->toArray();

        if($projetos_usuario != null){
            foreach($projetos_usuario as $projeto_usuario){
                $projeto = Projeto::find($projeto_usuario['projeto']);
                if($projeto){
                    $projetos[] = $projeto;
                }
            }
        }

        return response()->json($projetos);
    }

}
