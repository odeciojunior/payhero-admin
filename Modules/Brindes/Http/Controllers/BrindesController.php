<?php

namespace Modules\Brindes\Http\Controllers;

use App\Brinde;
use App\TipoBrinde;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class BrindesController extends Controller
{

    public function index() {

        return view('brindes::index'); 
    }


    public function cadastro() {

        $tipo_brindes = TipoBrinde::all();

        return view('brindes::cadastro',[
            'tipo_brindes' => $tipo_brindes
        ]);
    }

    public function cadastrarBrinde(Request $request){

        $dados = $request->all();

        $brinde = Brinde::create($dados);


        $foto = $request->file('foto');

        if ($foto != null) {
            $nome_foto = 'brinde_' . $brinde->id . '_.' . $foto->getClientOriginalExtension();

            $foto->move(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO, $nome_foto);

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

        return redirect()->route('brindes');
    }

    public function editarBrinde($id){

        $brinde = Brinde::find($id);
        $tipo_brindes = TipoBrinde::all();

        return view('brindes::editar',[
            'brinde' => $brinde,
            'tipo_brindes' => $tipo_brindes
        ]);

    }

    public function updateBrinde(Request $request){

        $dados = $request->all();

        if($request->file('foto') == null){
            unset($dados['foto']);
        }
        if($dados['tipo_brinde'] == 1 && $request->file('link') == null){
            unset($dados['link']);
        }

        $brinde = Brinde::find($dados['id']);
        $brinde->update($dados);

        $foto = $request->file('foto');

        if ($foto != null) {
            $nome_foto = 'brinde_' . $brinde->id . '_.' . $foto->getClientOriginalExtension();

            $foto->move(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO, $nome_foto);

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

        return redirect()->route('brindes');
    }

    public function deletarBrinde($id){

        Brinde::find($id)->delete();

        return redirect()->route('brindes');

    }


    public function dadosBrindes() {

        $brindes = \DB::table('brindes as brinde')
            ->leftJoin('tipo_brindes as tipo_brinde','tipo_brinde.id','brinde.tipo_brinde')
            ->get([
                'brinde.id',
                'brinde.descricao',
                'brinde.titulo',
                'brinde.tipo_brinde',
                'tipo_brinde.descricao as tipo_descricao'
        ]);

        return Datatables::of($brindes)
        ->addColumn('detalhes', function ($brinde) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_brinde' data-placement='top' data-toggle='tooltip' title='Detalhes' brinde='".$brinde->id."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a href='/brindes/editar/$brinde->id' class='btn btn-outline btn-primary editar_brinde' data-placement='top' data-toggle='tooltip' title='Editar' brinde='".$brinde->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_brinde' data-placement='top' data-toggle='tooltip' title='Excluir' brinde='".$brinde->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }


    public function getDetalhesBrinde(Request $request){

        $dados = $request->all();

        $brinde = Brinde::find($dados['id_brinde']);
        $tipo_brinde = TipoBrinde::find($brinde->tipo_brinde);

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Título:</b></td>";
        $modal_body .= "<td>".$brinde->titulo."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Descrição:</b></td>";
        $modal_body .= "<td>".$brinde->descricao."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Tipo:</b></td>";
        $modal_body .= "<td>".$tipo_brinde->descricao."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "<img src='".url(CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO.$brinde->foto)."' style='width: 100%'>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }
}
