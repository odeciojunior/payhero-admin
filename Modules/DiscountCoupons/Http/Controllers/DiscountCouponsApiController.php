<?php

namespace Modules\DiscountCoupons\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Project;
use Modules\DiscountCoupons\Http\Requests\DiscountCouponsStoreRequest;
use Modules\DiscountCoupons\Http\Requests\DiscountCouponsUpdateRequest;
use Modules\DiscountCoupons\Transformers\DiscountCouponsResource;
use Vinkla\Hashids\Facades\Hashids;

class DiscountCouponsApiController extends Controller
{

    /**
     * @param $projectId
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index($projectId)
    {
        try {
            $discountCouponsModel = new DiscountCoupon();
            $projectModel = new Project();

            if (isset($projectId)) {
                $project = $projectModel->find(Hashids::decode($projectId)[0]);
                if (Gate::allows('edit', [$project])) {
                    $projectId = $project->id;
                    $coupons = $discountCouponsModel->whereHas('project', function ($query) use ($projectId) {
                        $query->where('project_id', $projectId);
                    });

                    return DiscountCouponsResource::collection($coupons->orderBy('id', 'DESC')->paginate(5));
                } else {
                    return response()->json([
                        'message' => 'Sem permissão para acessar os coupon',
                    ], 400);
                }
            } else {
                return response()->json([
                    'message' => 'Erro ao listar dados de cupons',
                ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar cupons (DiscountCouponsController - index)');
            report($e);

            return response()->json([
                'message' => 'Erro ao listar dados de cupons',
            ], 400);
        }
    }

    /**
     * @param DiscountCouponsStoreRequest $request
     * @param $projectId
     * @return JsonResponse
     */
    public function store(DiscountCouponsStoreRequest $request, $projectId)
    {
        try {
            if (isset($projectId)) {
                $discountCouponsModel = new DiscountCoupon();
                $projectModel = new Project();

                $requestData = $request->validated();
                $requestData["project_id"] = Hashids::decode($projectId)[0];
                $requestData['value'] = preg_replace("/[^0-9]/", "", $requestData['value']);

                $project = $projectModel->find($requestData["project_id"]);

                if (Gate::allows('edit', [$project])) {
                    $discountCouponSaved = $discountCouponsModel->create($requestData);
                    if ($discountCouponSaved) {
                        return response()->json([
                            'message' => 'Cupom criado com sucesso!',

                        ], 200);
                    } else {
                        return response()->json([
                            'message' => 'Erro ao tentar salvar cupom!',

                        ], 400);
                    }
                } else {
                    return response()->json([
                        'message' => 'Sem permissão para criar cupom neste projeto',

                    ], 400);
                }
            }
            return response()->json([
                'message' => 'Erro ao tentar salvar cupom!',

            ], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar cadastrar novo cupom de desconto (DiscountCouponsController - store)');
            report($e);

            return response()->json([
                'message' => 'Erro ao tentar salvar cupom!',

            ], 400);
        }
    }


    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse|DiscountCouponsResource
     */
    public function show($projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {
                $discountCouponsModel = new DiscountCoupon();
                $projectModel = new Project();

                $coupon = $discountCouponsModel->find(Hashids::decode($id)[0]);
                $project = $projectModel->find(Hashids::decode($projectId)[0]);

                if (Gate::allows('edit', [$project])) {
                    if ($coupon) {
                        return new DiscountCouponsResource($coupon);
                    } else {
                        return response()->json(['message' => 'Erro ao buscar Cupom'], 400);
                    }
                } else {
                    return response()->json([
                        'message' => 'Sem permissão para visualizar este cupom',
                    ], 400);
                }
            }
            return response()->json(['message' => 'Erro ao buscar Cupom'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar dados de um cupom (DiscountCouponsController - show)');
            report($e);
            return response()->json(['message' => 'Erro ao buscar Cupom'], 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function edit($projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {

                $discountCouponsModel = new DiscountCoupon();
                $projectModel = new Project();

                $coupon = $discountCouponsModel->find(Hashids::decode($id)[0]);
                $project = $projectModel->find(Hashids::decode($projectId)[0]);

                if (Gate::allows('edit', [$project])) {
                    if ($coupon) {
                        $coupon->makeHidden(['id', 'project_id']);
                        return response()->json($coupon, 200);
                    } else {
                        return response()->json(['message' => 'Erro ao atualizar Cupom'], 400);
                    }
                } else {
                    return response()->json([
                        'message' => 'Sem permissão para editar este cupom',
                    ], 400);
                }
            }
            return response()->json(['message' => 'Erro ao buscar Cupom'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar dados para atualizar cupom (DescountCouponsController - edit)');
            report($e);
            return response()->json(['message' => 'Erro ao atualizar Cupom'], 400);
        }
    }

    /**
     * @param DiscountCouponsUpdateRequest $request
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function update(DiscountCouponsUpdateRequest $request, $projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {
                $discountCouponsModel = new DiscountCoupon();
                $projectModel = new Project();

                $requestValidated = $request->validated();

                $coupon = $discountCouponsModel->find(Hashids::decode($id)[0]);
                $project =  $projectModel->find(Hashids::decode($projectId)[0]);;

                if (Gate::allows('edit', [$project])) {

                    $requestValidated['value'] = preg_replace("/[^0-9]/", "", $requestValidated['value']);

                    $couponUpdated = $coupon->update($requestValidated);

                    if ($couponUpdated) {
                        return response()->json('Sucesso', 200);
                    } else {
                        return response()->json(['message' => 'Erro ao atualizar Cupom'], 400);
                    }
                } else {
                    return response()->json([
                        'message' => 'Sem permissão para atualizar este cupom',

                    ], 400);
                }
            }
            return response()->json(['message' => 'Erro ao atualizar Cupom'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualizar cupom de desconto  (DescountCouponController - update)');
            report($e);
            return response()->json(['message' => 'Erro ao atualizar Cupom'], 400);
        }
    }

    /**
     * @param $projectId
     * @param $id
     * @return JsonResponse
     */
    public function destroy($projectId, $id)
    {
        try {
            if (isset($id)) {
                $discountCouponsModel = new DiscountCoupon();
                $projectModel = new Project();

                $descountCoupon = $discountCouponsModel->find(Hashids::decode($id)[0]);
                $project = $projectModel->find( Hashids::decode($projectId)[0]);

                if (Gate::allows('edit', [$project])) {
                    $descountCoupon->delete();
                    if ($descountCoupon) {
                        return response()->json('Sucesso');
                    } else {
                        return response()->json('Erro');
                    }
                } else {
                    return response()->json([
                        'message' => 'Sem permissão para remover este cupom',

                    ], 400);
                }
            }
            return response()->json(['message' => 'Erro ao excluir Cupom'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir cupom de desconto (DescountCouponController - destroy)');
            report($e);
            return response()->json(['message' => 'Erro ao excluir Cupom'], 400);
        }
    }

}
