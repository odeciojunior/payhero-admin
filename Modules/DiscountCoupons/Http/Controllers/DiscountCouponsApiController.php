<?php

namespace Modules\DiscountCoupons\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

class DiscountCouponsApiController extends Controller {

    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $discountCouponsModel = new DiscountCoupon();
            $projectModel = new Project();

            if ($request->has('project') && !empty($request->input('project'))) {
                $projectId = current(Hashids::decode($request->input('project')));
                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {
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
                'message' => 'Erro ao listar dados de pixels',
            ], 400);
        }
    }

    /**
     * @param DiscountCouponsStoreRequest $request
     * @return JsonResponse
     */
    public function store(DiscountCouponsStoreRequest $request)
    {
        try {

            $discountCouponsModel = new DiscountCoupon();
            $projectModel = new Project();

            $requestData = $request->validated();
            $requestData["project_id"] = current(Hashids::decode($requestData['project_id']));
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
        } catch (Exception $e) {
            Log::warning('Erro ao tentar cadastrar novo cupom de desconto (DiscountCouponsController - store)');
            report($e);

            return response()->json([
                'message' => 'Erro ao tentar salvar cupom!',

            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $discountCouponsModel = new DiscountCoupon();
            $data = $request->all();
            if (isset($data['couponId'])) {
                $couponId = Hashids::decode($data['couponId'])[0];
                $coupon = $discountCouponsModel->with(['project'])->find($couponId);
                $project = $coupon->getRelation('project');

                if (Gate::allows('edit', [$project])) {
                    if ($coupon) {
                        $coupon->makeHidden(['id', 'project_id'])->unsetRelation('project');
                        return response()->json($coupon, 200);
                        //return view('discountcoupons::details', ['coupon' => $coupon]);
                    } else {
                        return response()->json(['message' => 'Erro ao buscar Cupom'], 400);
                    }
                } else {
                    return response()->json([
                        'message' => 'Sem permissão para visualizar este cupom',
                    ], 400);
                }
            }
            return response()->json(['message' => 'Erro ao buscar Cupom'], 404);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar dados de um cupom (DiscountCouponsController - show)');
            report($e);
            return response()->json(['message' => 'Erro ao buscar Cupom'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        try {
            $discountCouponsModel = new DiscountCoupon();
            $data = $request->all();
            if (isset($data['couponId'])) {
                $couponId = Hashids::decode($data['couponId'])[0];
                $coupon = $discountCouponsModel->with(['project'])->find($couponId);
                $project = $coupon->getRelation('project');

                if (Gate::allows('edit', [$project])) {
                    if ($coupon) {
                        $coupon->makeHidden(['id', 'project_id'])->unsetRelation('project');
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
            return response()->json(['message' => 'Erro ao buscar Cupom'], 404);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar dados para atualizar cupom (DescountCouponsController - edit)');
            report($e);
            return response()->json(['message' => 'Erro ao atualizar Cupom'], 400);
        }
    }

    /**
     * @param DiscountCouponsUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(DiscountCouponsUpdateRequest $request, $id)
    {
        try {
            $discountCouponsModel = new DiscountCoupon();
            $requestValidated = $request->validated();

            $couponId = current(Hashids::decode($id));
            $coupon = $discountCouponsModel->with(['project'])->find($couponId);
            $project = $coupon->getRelation('project');

            if (Gate::allows('edit', [$project])) {

                unset($requestValidated['project_id']);
                $requestValidated['value'] = preg_replace("/[^0-9]/", "", $requestValidated['value']);

                $couponUpdated = $coupon->update($requestValidated);

                if ($couponUpdated) {
                    return response()->json('Sucesso', 200);
                } else {
                    return response()->json('Erro');
                }
            } else {
                return response()->json([
                    'message' => 'Sem permissão para atualizar este cupom',

                ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualizar cupom de desconto  (DescountCouponController - update)');
            report($e);
            return response()->json(['message' => 'Erro ao atualizar Cupom'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        try {
            $discountCouponsModel = new DiscountCoupon();

            if (isset($id)) {
                $descountCouponId = Hashids::decode($id)[0];

                $descountCoupon = $discountCouponsModel->with(['project'])->find($descountCouponId);
                $project = $descountCoupon->getRelation('project');

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
            return response()->json(['message' => 'Erro ao excluir Cupom'], 404);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir cupom de desconto (DescountCouponController - destroy)');
            report($e);
            return response()->json(['message' => 'Erro ao excluir Cupom'], 400);
        }
    }

}
