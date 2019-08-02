<?php

namespace Modules\DiscountCoupons\Http\Controllers;

use App\Entities\DiscountCoupon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\DiscountCoupons\Http\Requests\DiscountCouponsStoreRequest;
use Modules\DiscountCoupons\Http\Requests\DiscountCouponsUpdateRequest;
use Modules\DiscountCoupons\Transformers\DiscountCouponsResource;
use Vinkla\Hashids\Facades\Hashids;

class DiscountCouponsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $discountCouponsModel = new DiscountCoupon();

            if ($request->has('project') && !empty($request->input('project'))) {
                $projectId = current(Hashids::decode($request->input('project')));
                $coupons   = $discountCouponsModel->whereHas('project', function($query) use ($projectId) {
                    $query->where('project', $projectId);
                });

                return DiscountCouponsResource::collection($coupons->orderBy('id', 'DESC')->paginate(5));
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function create()
    {
        try {
            return view('discountcoupons::create');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar redirecionar para tela de cadastro de cupons (DiscountCouponsController - create)');
            report($e);
        }
    }

    /**
     * @param DiscountCouponsStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DiscountCouponsStoreRequest $request)
    {
        try {

            $discountCouponsModel = new DiscountCoupon();

            $requestData            = $request->validated();
            $requestData["project"] = current(Hashids::decode($requestData['project']));
            $requestData['value']   = preg_replace("/[^0-9]/", "", $requestData['value']);

            $discountCouponSaved = $discountCouponsModel->create($requestData);
            if ($discountCouponSaved) {
                return response()->json('Cupom criado com Sucesso', 200);
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
            $discountCouponsModel = new DiscountCoupon();
            $data                 = $request->all();
            if (isset($data['couponId'])) {
                $couponId = Hashids::decode($data['couponId'])[0];
                $coupon   = $discountCouponsModel->find($couponId);
                if ($coupon) {
                    return view('discountcoupons::details', ['coupon' => $coupon]);
                }
            }

            return response()->json('Erro ao buscar Cupom');
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
            $discountCouponsModel = new DiscountCoupon();
            $data                 = $request->all();
            if (isset($data['couponId'])) {
                $couponId = Hashids::decode($data['couponId'])[0];
                $coupon   = $discountCouponsModel->find($couponId);
                if ($coupon) {
                    return view('discountcoupons::edit', ['coupon' => $coupon]);
                }
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar dados para atualizar cupom (DescountCouponsController - edit)');
            report($e);
        }
    }

    /**
     * @param DiscountCouponsUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DiscountCouponsUpdateRequest $request, $id)
    {
        try {
            $discountCouponsModel = new DiscountCoupon();
            $requestValidated     = $request->validated();

            $couponId = current(Hashids::decode($id));
            $coupon   = $discountCouponsModel->find($couponId);
            unset($requestValidated['project']);
            $requestValidated['value'] = preg_replace("/[^0-9]/", "", $requestValidated['value']);

            $couponUpdated = $coupon->update($requestValidated);

            if ($couponUpdated) {
                return response()->json('Sucesso', 200);
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualizar cupom de desconto  (DescountCouponController - update)');
            dd($e);
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
            $discountCouponsModel = new DiscountCoupon();

            if (isset($id)) {
                $descountCouponId = Hashids::decode($id)[0];
                $descountCoupon   = $discountCouponsModel->find($descountCouponId)->delete();
                if ($descountCoupon) {
                    return response()->json('Sucesso');
                }
            }

            return response()->json('Erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir cupom de desconto (DescountCouponController - destroy)');
            report($e);
        }
    }
}
