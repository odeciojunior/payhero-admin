<?php

namespace Modules\TiposBrindes\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class TiposBrindesController extends Controller
{
    public function index() {

        return view('tiposbrindes::index'); 
    }

    public function cadastro() {

        return view('tiposbrindes::cadastro');
    }

    public function cadastrarTipoBrinde(Request $request){

        $dados = $request->all();

        TipoBrinde::create($dados);

        return redirect()->route('tiposbrindes');
    }

    public function editarTipoBrinde($id){

        $tipo_brinde = TipoBrinde::find($id);

        return view('tiposbrindes::editar',[
            'tipo_brinde' => $tipo_brinde,
        ]);

    }

    public function updateTipoBrinde(Request $request){

        $dados = $request->all();

        TipoBrinde::find($dados['id'])->update($dados);

        return redirect()->route('tiposbrindes');
    }

    public function deletarTipoBrinde($id){

        TipoBrinde::find($id)->delete();

        return redirect()->route('tiposbrindes');

    }

    public function dadosTiposBrindes() {

        $tiposbrindes = \DB::table('tipo_brindes')
            ->get([
                'id',
                'descricao',
        ]);

        return Datatables::of($tiposbrindes)
        ->addColumn('detalhes', function ($tiposbrindes) {
            return "<span data-toggle='modal' data-target='#modal_editar'>
                        <a href='#' class='btn btn-outline btn-primary editar_tiposbrindes' data-placement='top' data-toggle='tooltip' title='Editar' tiposbrindes='".$tiposbrindes->id."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger' data-placement='top' data-toggle='tooltip' title='Excluir' tiposbrindes='".$tiposbrindes->id."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesTiposbrindes(Request $request){

        $dados = $request->all();

        $tiposbrindes = TipoBrinde::find($dados['id_tiposbrindes']);

        $produtos = Produto::where('tiposbrindes', $tiposbrindes->id)->get()->toArray();

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome:</b></td>";
        $modal_body .= "<td>".$tiposbrindes->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Descrição:</b></td>";
        $modal_body .= "<td>".$tiposbrindes->descricao."</td>";
        $modal_body .= "</tr>";
        foreach($produtos as $produto){
            $modal_body .= "<tr>";
            $modal_body .= "<td><b>Produto:</b></td>";
            $modal_body .= "<td>".$produto['nome']."</td>";
            $modal_body .= "</tr>";
        }
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }
}
