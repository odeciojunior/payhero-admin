<?php

namespace Modules\CuponsDesconto\Http\Controllers;

use App\Cupom;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;

class CuponsDescontoController extends Controller {

    public function index() {

        return view('cuponsdesconto::index'); 
    }

    public function cadastro() {

        return view('cuponsdesconto::cadastro');
    }

    public function cadastrarCupomDesconto(Request $request){

        $dados = $request->all();
        $dados['projeto'] = Hashids::decode($dados['projeto'])[0];

        Cupom::create($dados);

        return response()->json('Sucesso');
    }

    public function editarCupomDesconto($id){

        $cupom_desconto = Cupom::where('id',Hashids::decode($id)[0])->first();

        return view('cuponsdesconto::editar',[
            'cupom' => $cupom_desconto,
        ]);

    }

    public function updateCupomDesconto(Request $request){

        $dados = $request->all();
        unset($dados['projeto']);
        $cupom = Cupom::where('id',Hashids::decode($dados['id']))->first();
        $cupom->update($dados);

        return response()->json('Sucesso');
    }

    public function deletarCupomDesconto(Request $request){

        $dados = $request->all();

        $cupom = Cupom::where('id',Hashids::decode($dados['id']))->first();

        $cupom->delete();

        return response()->json('Sucesso');

    }

    public function dadosCuponsDesconto(Request $request) {

        $dados = $request->all();

        $cupons = \DB::table('cupons as cupom');

        if(isset($dados['projeto'])){
            $cupons = $cupons->where('cupom.projeto','=', Hashids::decode($dados['projeto']));
        }
        else{
            return response()->json('projeto não encontrado');
        }

        $cupons = $cupons->get([
                'cupom.id',
                'cupom.nome',
                'cupom.tipo',
                'cupom.valor',
                'cupom.cod_cupom',
                'cupom.status',
        ]);

        return Datatables::of($cupons)
        ->editColumn('tipo', function ($cupom) {
            if($cupom->tipo)
                return "Valor";
            else
                return "Porcentagem";
        })
        ->editColumn('status', function ($cupom) {
            if($cupom->status)
                return "Ativo";
            else
                return "Inativo";
        })
        ->addColumn('detalhes', function ($cupom) {
            return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_cupom' data-placement='top' data-toggle='tooltip' title='Detalhes' cupom='".Hashids::encode($cupom->id)."'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_cupom' data-placement='top' data-toggle='tooltip' title='Editar' cupom='".Hashids::encode($cupom->id)."'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_cupom' data-placement='top' data-toggle='tooltip' title='Excluir' cupom='".Hashids::encode($cupom->id)."'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
        })
        ->rawColumns(['detalhes'])
        ->make(true);
    }

    public function getDetalhesCupomDesconto(Request $request){

        $dados = $request->all();

        $cupom = Cupom::where('id',Hashids::decode($dados['id_cupom']))->first();

        $modal_body = '';

        $modal_body .= "<div class='col-xl-12 col-lg-12'>";
        $modal_body .= "<table class='table table-bordered table-hover table-striped'>";
        $modal_body .= "<thead>";
        $modal_body .= "</thead>";
        $modal_body .= "<tbody>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Nome:</b></td>";
        $modal_body .= "<td>".$cupom->nome."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Descrição:</b></td>";
        $modal_body .= "<td>".$cupom->descricao."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Código:</b></td>";
        $modal_body .= "<td>".$cupom->cod_cupom."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Tipo:</b></td>";
        if($cupom->tipo)
            $modal_body .= "<td>Valor</td>";
        else
            $modal_body .= "<td>Porcentagem</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Valor:</b></td>";
        $modal_body .= "<td>".$cupom->valor."</td>";
        $modal_body .= "</tr>";
        $modal_body .= "<tr>";
        $modal_body .= "<td><b>Status:</b></td>";
        if($cupom->status)
            $modal_body .= "<td>Ativo</td>";
        else
            $modal_body .= "<td>Inativo</td>";
        $modal_body .= "</tr>";
        $modal_body .= "</thead>";
        $modal_body .= "</table>";
        $modal_body .= "</div>";
        $modal_body .= "</div>";

        return response()->json($modal_body);
    }

    public function getFormAddCupom(Request $request){

        $form = view('cuponsdesconto::cadastro');

        return response()->json($form->render());
    }

    public function getFormEditarCupom(Request $request){

        $dados = $request->all();

        $cupom = Cupom::where('id',Hashids::decode($dados['id'])[0])->first();

        $id = Hashids::encode($cupom['id']);

        $form = view('cuponsdesconto::editar',[
            'cupom' => $cupom,
            'id' => $id
        ]);

        return response()->json($form->render());
    }


}
