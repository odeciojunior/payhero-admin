<?php

namespace Modules\Layouts\Http\Controllers;

use App\Layout;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class LayoutsController extends Controller
{
    public function index() {

        return view('layouts::index');  
    }

    public function cadastro() {

        return view('layouts::cadastro');
    }

    public function cadastrarLayout(Request $request){

        $dados = $request->all();

        if($dados['estilo'] == 'Padrao'){
            $dados['cor1'] = $dados['cor1-padrao'];
            $dados['cor2'] = '';
        }
        elseif($dados['estilo'] == 'Backgoud Multi Camada'){
            $dados['cor1'] = $dados['cor1-multi-camadas'];
            $dados['cor2'] = $dados['cor2-multi-camadas'];
        }

        $layout = Layout::create($dados);

        $logo = $request->file('logo');

        if ($logo != null) {
            $nome_logo = 'logo_' . $layout->id . '_.' . $logo->getClientOriginalExtension();

            $logo->move(CaminhoArquivosHelper::CAMINHO_FOTO_LOGO, $nome_logo);

            $img = Image::make(base_path() . '/public/' . CaminhoArquivosHelper::CAMINHO_FOTO_LOGO. $nome_logo)->resize(
                200,
                200
            );
            $img->save(base_path() . '/public/' . CaminhoArquivosHelper::CAMINHO_FOTO_LOGO. $nome_logo);

            $layout->update([
                'logo' => $nome_logo
            ]);
        }

        return redirect()->route('layouts');
    }

    public function editarLayout($id){

        $layout = Layout::find($id);

        return view('layouts::editar',[
            'layout' => $layout,
        ]);

    }

    public function updateLayout(Request $request){

        $dados = $request->all();

        if($dados['estilo'] == 'Padrao'){
            $dados['cor1'] = $dados['cor1-padrao'];
            $dados['cor2'] = '';
        }
        elseif($dados['estilo'] == 'Backgoud Multi Camada'){
            $dados['cor1'] = $dados['cor1-multi-camadas'];
            $dados['cor2'] = $dados['cor2-multi-camadas'];
        }

        $layout = Layout::find($dados['id']);

        $layout->update($dados);

        $logo = $request->file('logo');

        if ($logo != null) {
            $nome_logo = 'logo_' . $layout->id . '_.' . $logo->getClientOriginalExtension();

            $logo->move(CaminhoArquivosHelper::CAMINHO_FOTO_LOGO, $nome_logo);

            $img = Image::make(base_path() . '/public/' . CaminhoArquivosHelper::CAMINHO_FOTO_LOGO. $nome_logo)->resize(
                200,
                200
            );
            $img->save(base_path() . '/public/' . CaminhoArquivosHelper::CAMINHO_FOTO_LOGO. $nome_logo);

            $layout->update([
                'logo' => $nome_logo
            ]);
        }

        return redirect()->route('layouts');
    }

    public function deletarLayout($id){

        Layout::find($id)->delete();

        return redirect()->route('layouts');

    }

    public function dadosLayout() {

        $layouts = \DB::table('layouts as layout')
            ->get([
                'layout.id',
                'layout.descricao',
                'layout.logo',
                'layout.estilo',
                'layout.cor1',
                'layout.cor2',
                'layout.botao',
        ]);

        return Datatables::of($layouts)
        ->addColumn('detalhes', function ($layout) {
            return "<span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/layouts/editar/$layout->id' class='btn btn-outline btn-primary editar_layout' data-placement='top' data-toggle='tooltip' title='Editar' layout='".$layout->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_layout' data-placement='top' data-toggle='tooltip' title='Excluir' layout='".$layout->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

}
