<?php

namespace Modules\Gifts\Http\Controllers;

use App\Entities\Gift;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;


class GiftsController extends Controller {

    public function index(Request $request) {

        $requestData = $request->all();

        $gifts = \DB::table('gifts as gift');

        if(isset($requestData['projeto'])){
            $gifts = $gifts->where('gift.project','=', Hashids::decode($requestData['projeto']));
        }

        $gifts = $gifts->get([
                'gift.id',
                'gift.description',
                'gift.title',
                'gift.type',
        ]);

        return Datatables::of($gifts)
        ->addColumn('detalhes', function ($gift) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_brinde' data-placement='top' data-toggle='tooltip' title='Detalhes' brinde='".Hashids::encode($gift->id)."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_brinde' data-placement='top' data-toggle='tooltip' title='Editar' brinde='".Hashids::encode($gift->id)."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_brinde' data-placement='top' data-toggle='tooltip' title='Excluir' brinde='".Hashids::encode($gift->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function details(Request $request){

        $requestData = $request->all();

        $gift = Gift::where('id',Hashids::decode($requestData['id_brinde']))->first();

        $tipoBrinde = TipoGift::find($gift->tipo_brinde);

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Título:</b></td>";
        $modalBody .= "<td>".$gift->titulo."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Descrição:</b></td>";
        $modalBody .= "<td>".$gift->descricao."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Tipo:</b></td>";
        $modalBody .= "<td>".$tipoBrinde['descricao']."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "<img src='".url(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO.$gift->foto)."?dummy=".uniqid()."'>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }

    public function store(Request $request){

        $requestData = $request->all();
        $requestData['projeto'] = Hashids::decode($requestData['projeto'])[0];

        $gift = Gift::create($requestData);

        $foto = $request->file('foto_brinde_cadastrar');

        if ($foto != null) {
            $nome_foto = 'brinde_' . $gift->id . '_.' . $foto->getClientOriginalExtension();

            Storage::delete('public/upload/brindes/fotos/'.$nome_foto);
 
            $foto->move(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO, $nome_foto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO . $nome_foto);

            $img->crop($requestData['foto_brinde_cadastrar_w'], $requestData['foto_brinde_cadastrar_h'], $requestData['foto_brinde_cadastrar_x1'], $requestData['foto_brinde_cadastrar_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/brindes/fotos/'.$nome_foto);
            
            $img->save(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO . $nome_foto);

            $gift->update([
                'foto' => $nome_foto,
            ]);
        }

        if($requestData['tipo_brinde'] == 1 && $request->file('link') != null){

            $arquivo = 'arquivo_' . $gift->id . '_.' . $request->file('link')->getClientOriginalExtension();

            $request->file('link')->move(CaminhoArquivosHelper::CAMINHO_BRINDES_EBOOK, $arquivo);

            $gift->update([
                'link' => $arquivo,
            ]);

        }

        return response()->json('Sucesso');
    }

    public function update(Request $request){

        $requestData = $request->all();
        unset($requestData['projeto']);

        if($request->file('foto') == null){
            unset($requestData['foto']);
        }
        if($requestData['tipo_brinde'] == 1 && $request->file('link') == null){
            unset($requestData['link']);
        }

        $gift = Gift::where('id',Hashids::decode($requestData['id']))->first();
        $gift->update($requestData);

        $foto = $request->file('foto_brinde_editar');

        if ($foto != null) {
            $nome_foto = 'brinde_' . $gift['id'] . '_.' . $foto->getClientOriginalExtension();

            Storage::delete('public/upload/brindes/fotos/'.$gift['foto']);

            $foto->move(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO, $nome_foto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO . $nome_foto);

            $img->crop($requestData['foto_brinde_editar_w'], $requestData['foto_brinde_editar_h'], $requestData['foto_brinde_editar_x1'], $requestData['foto_brinde_editar_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/brindes/fotos/'.$nome_foto);

            $img->save(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO . $nome_foto);

            $gift->update([
                'foto' => $nome_foto,
            ]);
        }

        if($requestData['tipo_brinde'] == 1 && $request->file('link') != null){

            $arquivo = 'arquivo_' . $gift->id . '_.' . $request->file('link')->getClientOriginalExtension();

            $request->file('link')->move(CaminhoArquivosHelper::CAMINHO_BRINDES_EBOOK, $arquivo);

            $gift->update([
                'link' => $arquivo,
            ]);

        }

        return response()->json('Sucesso');
    }

    public function delete(Request $request){

        $requestData = $request->all();

        $gift = Gift::where('id',Hashids::decode($requestData['id']))->first();
        $gift->delete();

        // Storage::delete('public/upload/brindes/fotos/'.$gift['foto']);

        $gift->delete();

        return response()->json('Sucesso');

    }

    public function create(){

        $tipoBrindes = TipoGift::all();

        $form = view('brindes::cadastro',[
            'tipo_brindes' => $tipoBrindes
        ]);

        return response()->json($form->render());
    }

    public function edit(Request $request){

        $requestData = $request->all();

        $gift = Gift::where('id',Hashids::decode($requestData['id']))->first();
        $idBrinde = Hashids::encode($gift->id);

        $tipoBrindes = TipoGift::all();

        $form = view('brindes::editar',[
            'id_brinde'     => $idBrinde,
            'brinde'        => $gift,
            'tipo_brindes'  => $tipoBrindes
        ]);

        return response()->json($form->render());
    }

}


