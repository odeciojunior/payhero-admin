<?php

namespace Modules\DiscountCoupons\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Entities\DiscountCoupon;
use Illuminate\Routing\Controller;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;

class DiscountCouponsController extends Controller {

    public function index(Request $request) {

        $requestData = $request->all();

        $cupons = \DB::table('discount_coupons as cupom');

        if(isset($requestData['projeto'])){
            $cupons = $cupons->where('cupom.project','=', Hashids::decode($requestData['projeto']));
        }
        else{
            return response()->json('projeto não encontrado');
        }

        $cupons = $cupons->get([
                'cupom.id',
                'cupom.name',
                'cupom.type',
                'cupom.value',
                'cupom.code',
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

    public function update(Request $request){

        $requestData = $request->all();
        unset($requestData['projeto']);
        $cupom = DiscountCoupon::where('id',Hashids::decode($requestData['id']))->first();
        $cupom->update($requestData);

        return response()->json('Sucesso');
    }

    public function delete(Request $request){

        $requestData = $request->all();

        $cupom = DiscountCoupon::where('id',Hashids::decode($requestData['id']))->first();

        $cupom->delete();

        return response()->json('Sucesso');

    }

    public function details(Request $request){

        $requestData = $request->all();

        $cupom = DiscountCoupon::where('id',Hashids::decode($requestData['id_cupom']))->first();

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Nome:</b></td>";
        $modalBody .= "<td>".$cupom->nome."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Descrição:</b></td>";
        $modalBody .= "<td>".$cupom->descricao."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Código:</b></td>";
        $modalBody .= "<td>".$cupom->cod_cupom."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Tipo:</b></td>";
        if($cupom->tipo)
            $modalBody .= "<td>Valor</td>";
        else
            $modalBody .= "<td>Porcentagem</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Valor:</b></td>";
        $modalBody .= "<td>".$cupom->valor."</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Status:</b></td>";
        if($cupom->status)
            $modalBody .= "<td>Ativo</td>";
        else
            $modalBody .= "<td>Inativo</td>";
        $modalBody .= "</tr>";
        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
    }

    public function create(Request $request){

        $form = view('discountcoupons::create');

        return response()->json($form->render());
    }

    public function edit(Request $request){

        $requestData = $request->all();

        $cupom = DiscountCoupon::where('id',Hashids::decode($requestData['id'])[0])->first();

        $id = Hashids::encode($cupom['id']);

        $form = view('discountcoupons::edit',[
            'cupom' => $cupom,
            'id'    => $id
        ]);

        return response()->json($form->render());
    }


}
