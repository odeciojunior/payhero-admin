<?php

namespace Modules\DiscountCoupons\Http\Controllers;

use App\Entities\DiscountCoupon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;

class DiscountCouponsController extends Controller
{
    private $discountCouponsModel;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDiscountCoupons()
    {
        if (!$this->discountCouponsModel) {
            $this->discountCouponsModel = app(DiscountCoupon::class);
        }

        return $this->discountCouponsModel;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $requestData = $request->all();

            if (isset($requestData['projeto'])) {
                $projectId = Hashids::decode($request['projeto'])[0];
                $cupons    = $this->getDiscountCoupons()->whereHas('project', function($query) use ($projectId) {
                    $query->where('project', $projectId);
                })->get();
            } else {
                return response()->json('Projeto não encontrado');
            }

            return Datatables::of($cupons)
                             ->editColumn('type', function($cupom) {
                                 if ($cupom->type)
                                     return "Valor";
                                 else
                                     return "Porcentagem";
                             })
                             ->editColumn('status', function($cupom) {
                                 if ($cupom->status)
                                     return "Ativo";
                                 else
                                     return "Inativo";
                             })
                             ->addColumn('detalhes', function($cupom) {
                                 return "<span data-toggle='modal' data-target='#modal_detalhes'>
                        <a class='btn btn-outline btn-success detalhes_cupom' data-placement='top' data-toggle='tooltip' title='Detalhes' cupom='" . Hashids::encode($cupom->id) . "'>
                            <i class='icon wb-order' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_editar'>
                        <a class='btn btn-outline btn-primary editar_cupom' data-placement='top' data-toggle='tooltip' title='Editar' cupom='" . Hashids::encode($cupom->id) . "'>
                            <i class='icon wb-pencil' aria-hidden='true'></i>
                        </a>
                    </span>
                    <span data-toggle='modal' data-target='#modal_excluir'>
                        <a class='btn btn-outline btn-danger excluir_cupom' data-placement='top' data-toggle='tooltip' title='Excluir' cupom='" . Hashids::encode($cupom->id) . "'>
                            <i class='icon wb-trash' aria-hidden='true'></i>
                        </a>
                    </span>";
                             })
                             ->rawColumns(['detalhes'])
                             ->make(true);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar cupons (DiscountCouponsController - index)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create()
    {
        try {
            $view = view('discountcoupons::create');

            return response()->json($view->render());
        } catch (Exception $e) {
            Log::warning('Erro ao tentar redirecionar para tela de cadastro de cupons (DiscountCouponsController - create)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $requestData            = $request->all();
            $requestData["project"] = Hashids::decode($requestData['project'])[0];
            $discountCouponSaved    = $this->getDiscountCoupons()->create($requestData);
            if ($discountCouponSaved) {

                return response()->json('Sucesso');
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar cadastrar novo cupom de desconto (DiscountCouponsController - store)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        try {

            $idCoupom = $request->input('id_cupom');
            if ($idCoupom) {
                $idCoupom = Hashids::decode($idCoupom)[0];
                $cupom    = $this->getDiscountCoupons()->where('id', $idCoupom)->first();
                if ($cupom) {
                    $modalBody = '';

                    $modalBody .= "<div class='col-xl-12 col-lg-12'>";
                    $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
                    $modalBody .= "<thead>";
                    $modalBody .= "</thead>";
                    $modalBody .= "<tbody>";
                    $modalBody .= "<tr>";
                    $modalBody .= "<td><b>Nome:</b></td>";
                    $modalBody .= "<td>" . $cupom->name . "</td>";
                    $modalBody .= "</tr>";
                    /* $modalBody .= "<tr>";
                     $modalBody .= "<td><b>Descrição:</b></td>";
                     $modalBody .= "<td>" . $cupom->descricao . "</td>";
                     $modalBody .= "</tr>";*/
                    $modalBody .= "<tr>";
                    $modalBody .= "<td><b>Código:</b></td>";
                    $modalBody .= "<td>" . $cupom->code . "</td>";
                    $modalBody .= "</tr>";
                    $modalBody .= "<tr>";
                    $modalBody .= "<td><b>Tipo:</b></td>";
                    if ($cupom->tipo)
                        $modalBody .= "<td>Valor</td>";
                    else
                        $modalBody .= "<td>Porcentagem</td>";
                    $modalBody .= "</tr>";
                    $modalBody .= "<tr>";
                    $modalBody .= "<td><b>Valor:</b></td>";
                    $modalBody .= "<td>" . $cupom->value . "</td>";
                    $modalBody .= "</tr>";
                    $modalBody .= "<tr>";
                    $modalBody .= "<td><b>Status:</b></td>";
                    if ($cupom->status)
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
            }

            return response()->json('Erro ao tentar buscar cupom');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar dados de um cupom (DiscountCouponsController - show)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function edit(Request $request)
    {
        try {
            $idCupom = $request->input('idCupom');

            if ($idCupom) {
                $idCupom = Hashids::decode($idCupom)[0];
                $cupom   = $this->getDiscountCoupons()->find($idCupom);
                if ($cupom) {
                    $view = view("discountcoupons::edit", ['cupom' => $cupom, 'id' => $idCupom]);

                    return response()->json($view->render());
                }
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar dados para atualizar cupom (DescountCouponsController - edit)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $data          = $request->input('coupomData');
            $coupomId      = Hashids::decode($data['id'])[0];
            $coupom        = $this->getDiscountCoupons()->find($coupomId);
            $coupomUpdated = $coupom->update($data);
            if ($coupomUpdated) {
                return response()->json('Sucesso');
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualizar cupom de desconto  (DescountCouponController - update)');
            report($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $descountCouponId = Hashids::decode($id)[0];
            $descountCoupon   = $this->getDiscountCoupons()->find($descountCouponId)->delete();
            if ($descountCoupon) {

                return response()->json('Sucesso');
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir cupom de desconto (DescountCouponController - destroy)');
            report($e);
        }
    }
}
