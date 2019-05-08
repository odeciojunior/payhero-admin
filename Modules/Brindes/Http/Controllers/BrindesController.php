<?php

namespace Modules\Brindes\Http\Controllers;

use App\Brinde;
use App\TipoBrinde;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;


class BrindesController extends Controller {


    public function index() {

        return view('brindes::index'); 
    }

    public function cadastro() {

        $tipoBrindes = TipoBrinde::all();

        return view('brindes::cadastro',[
            'tipo_brindes' => $tipoBrindes
        ]);
    }

    public function cadastrarBrinde(Request $request){

        $dados = $request->all();
        $dados['projeto'] = Hashids::decode($dados['projeto'])[0];

        $brinde = Brinde::create($dados);

        $foto = $request->file('foto_brinde_cadastrar');

        if ($foto != null) {
            $nome_foto = 'brinde_' . $brinde->id . '_.' . $foto->getClientOriginalExtension();

            Storage::delete('public/upload/brindes/fotos/'.$nome_foto);
 
            $foto->move(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO, $nome_foto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO . $nome_foto);

            $img->crop($dados['foto_brinde_cadastrar_w'], $dados['foto_brinde_cadastrar_h'], $dados['foto_brinde_cadastrar_x1'], $dados['foto_brinde_cadastrar_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/brindes/fotos/'.$nome_foto);
            
            $img->save(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO . $nome_foto);

            $brinde->update([
                'foto' => $nome_foto,
            ]);
        }

        if($dados['tipo_brinde'] == 1 && $request->file('link') != null){

            $arquivo = 'arquivo_' . $brinde->id . '_.' . $request->file('link')->getClientOriginalExtension();

            $request->file('link')->move(CaminhoArquivosHelper::CAMINHO_BRINDES_EBOOK, $arquivo);

            $brinde->update([
                'link' => $arquivo,
            ]);

        }

        return response()->json('Sucesso');
    }

    public function editarBrinde($id){

        $brinde = Brinde::where('id',Hashids::decode($id))->first();
        $idBrinde = Hashids::encode($brinde->id);

        $tipoBrindes = TipoBrinde::all();

        return view('brindes::editar',[
            'id_brinde' => $idBrinde,
            'brinde' => $brinde,
            'tipo_brindes' => $tipoBrindes
        ]);

    }

    public function updateBrinde(Request $request){

        $dados = $request->all();
        unset($dados['projeto']);

        if($request->file('foto') == null){
            unset($dados['foto']);
        }
        if($dados['tipo_brinde'] == 1 && $request->file('link') == null){
            unset($dados['link']);
        }

        $brinde = Brinde::where('id',Hashids::decode($dados['id']))->first();
        $brinde->update($dados);

        $foto = $request->file('foto_brinde_editar');

        if ($foto != null) {
            $nome_foto = 'brinde_' . $brinde['id'] . '_.' . $foto->getClientOriginalExtension();

            Storage::delete('public/upload/brindes/fotos/'.$brinde['foto']);

            $foto->move(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO, $nome_foto);

            $img = Image::make(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO . $nome_foto);

            $img->crop($dados['foto_brinde_editar_w'], $dados['foto_brinde_editar_h'], $dados['foto_brinde_editar_x1'], $dados['foto_brinde_editar_y1']);

            $img->resize(200, 200);

            Storage::delete('public/upload/brindes/fotos/'.$nome_foto);

            $img->save(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO . $nome_foto);

            $brinde->update([
                'foto' => $nome_foto,
            ]);
        }

        if($dados['tipo_brinde'] == 1 && $request->file('link') != null){

            $arquivo = 'arquivo_' . $brinde->id . '_.' . $request->file('link')->getClientOriginalExtension();

            $request->file('link')->move(CaminhoArquivosHelper::CAMINHO_BRINDES_EBOOK, $arquivo);

            $brinde->update([
                'link' => $arquivo,
            ]);

        }

        return response()->json('Sucesso');
    }

    public function deletarBrinde(Request $request){

        $dados = $request->all();

        $brinde = Brinde::where('id',Hashids::decode($dados['id']))->first();
        $brinde->delete();

        // Storage::delete('public/upload/brindes/fotos/'.$brinde['foto']);

        $brinde->delete();

        return response()->json('Sucesso');

    }

    public function dadosBrindes(Request $request) {

        $dados = $request->all();

        $brindes = \DB::table('brindes as brinde')
            ->leftJoin('tipo_brindes as tipo_brinde','tipo_brinde.id','brinde.tipo_brinde');

        if(isset($dados['projeto'])){
            $brindes = $brindes->where('brinde.projeto','=', Hashids::decode($dados['projeto']));
        }

        $brindes = $brindes->get([
                'brinde.id',
                'brinde.descricao',
                'brinde.titulo',
                'brinde.tipo_brinde',
                'tipo_brinde.descricao as tipo_descricao'
        ]);

        return Datatables::of($brindes)
        ->addColumn('detalhes', function ($brinde) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_brinde' data-placement='top' data-toggle='tooltip' title='Detalhes' brinde='".Hashids::encode($brinde->id)."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_brinde' data-placement='top' data-toggle='tooltip' title='Editar' brinde='".Hashids::encode($brinde->id)."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_brinde' data-placement='top' data-toggle='tooltip' title='Excluir' brinde='".Hashids::encode($brinde->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesBrinde(Request $request){

        $dados = $request->all();

        $brinde = Brinde::where('id',Hashids::decode($dados['id_brinde']))->first();

        $tipoBrinde = TipoBrinde::find($brinde->tipo_brinde);

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Título:</b></td>";
        $modalBody .= "<td>".$brinde->titulo."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Descrição:</b></td>";
        $modalBody .= "<td>".$brinde->descricao."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Tipo:</b></td>";
        $modalBody .= "<td>".$tipoBrinde['descricao']."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "<img src='".url(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO.$brinde->foto)."?dummy=".uniqid()."'>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }

    public function getFormAddBrinde(){

        $tipoBrindes = TipoBrinde::all();

        $form = view('brindes::cadastro',[
            'tipo_brindes' => $tipoBrindes
        ]);

        return response()->json($form->render());
    }

    public function getFormEditarBrinde(Request $request){

        $dados = $request->all();

        $brinde = Brinde::where('id',Hashids::decode($dados['id']))->first();
        $idBrinde = Hashids::encode($brinde->id);

        $tipoBrindes = TipoBrinde::all();

        $form = view('brindes::editar',[
            'id_brinde'     => $idBrinde,
            'brinde'        => $brinde,
            'tipo_brindes'  => $tipoBrindes
        ]);

        return response()->json($form->render());
    }

}


