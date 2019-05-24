<?php

namespace Modules\Layouts\Http\Controllers;

use App\Entities\Layout;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class LayoutsController extends Controller {
 

    public function index(Request $request) {

        $dados = $request->all();

        $layouts = \DB::table('layouts as layout');

        if(isset($dados['projeto'])){
            $layouts = $layouts->where('layout.project','=', Hashids::decode($dados['projeto'])[0]);
        }

        $layouts = $layouts->get([
                'layout.id',
                'layout.description',
                'layout.status',
        ]);

        return Datatables::of($layouts)
        ->addColumn('detalhes', function ($layout) {
            return "<span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_layout' data-placement='top' data-toggle='tooltip' title='Editar' layout='".$layout->id."'>
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

    public function store(Request $request){

        $dados = $request->all();

        $dados['project'] = Hashids::decode($dados['projeto'])[0];

        if($dados['status'] == 'Ativo'){
            $layouts = Layout::where('project',$dados['project'])->get()->toArray();
            foreach($layouts as $l){
                if($l['status'] == 'Ativo'){
                    Layout::find($l['id'])->update([
                        'status' => 'Desativado'
                    ]);
                }
            }
        }

        $layout = Layout::create($dados);

        $foto = $request->file('foto_checkout');

        if ($foto != null) {
            $nome_logo = 'logo_' . $layout->id . '_.' . $foto->getClientOriginalExtension();

            Storage::delete('public/upload/logo/'.$nome_logo);
            
            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_LOGO, $nome_logo);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_LOGO . $nome_logo);

            $img->crop($dados['foto_checkout_cadastrar_w'], $dados['foto_checkout_cadastrar_h'], $dados['foto_checkout_cadastrar_x1'], $dados['foto_checkout_cadastrar_y1']);

            if($dados['format_logo'] == 'quadrado')
                $img->resize(150, 150);
            else
                $img->resize(300, 150);

            Storage::delete('public/upload/logo/'.$nome_logo);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_LOGO . $nome_logo);

            $layout->update([
                'logo' => $nome_logo,
            ]);
        }

        return response()->json('sucesso');
    }

    public function update(Request $request){

        $dados = $request->all();

        $layout = Layout::find($dados['id']);

        $layout->update($dados);

        if($dados['status'] == 'Ativo'){
            $layouts = Layout::where([
                ['projeto',$dados['projeto']],
                ['id','!=',$layout['id']]
            ])->get()->toArray();

            foreach($layouts as $l){
                if($l['status'] == 'Ativo'){
                    Layout::find($l['id'])->update([
                        'status' => 'Desativado'
                    ]);
                }
            }
        }

        $foto = $request->file('foto_checkout');

        if ($foto != null) {

            $nome_logo = 'logo_' . $layout->id . '_.' . $foto->getClientOriginalExtension();

            Storage::delete('public/upload/logo/'.$nome_logo);
            
            $foto->move(CaminhoArquivosHelper::CAMINHO_FOTO_LOGO, $nome_logo);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_FOTO_LOGO . $nome_logo);

            $img->crop($dados['foto_checkout_editar_w'], $dados['foto_checkout_editar_h'], $dados['foto_checkout_editar_x1'], $dados['foto_checkout_editar_y1']);

            if($dados['formato_logo'] == 'quadrado')
                $img->resize(150, 150);
            else
                $img->resize(300, 150);

            Storage::delete('public/upload/logo/'.$nome_logo);

            $img->save(CaminhoArquivosHelper::CAMINHO_FOTO_LOGO . $nome_logo);

            $layout->update([
                'logo' => $nome_logo,
            ]);
        }

        return response()->json('sucesso');
    }

    public function delete(Request $request){

        $dados = $request->all();

        Layout::find($dados['id'])->delete();

        return response()->json('sucesso');

    }

    public function create(Request $request){

        $dados = $request->all();

        $form = view('layouts::create');

        return response()->json($form->render());
    }

    public function edit(Request $request){

        $dados = $request->all();

        $layout = Layout::find($dados['id']);

        $form = view('layouts::edit',[
            'layout' => $layout
        ]);

        return response()->json($form->render());
    }

}
