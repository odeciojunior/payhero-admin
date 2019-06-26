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

            if (isset($requestData['project'])) {
                $projectId = Hashids::decode($requestData['project'])[0];
                $coupons   = $this->getDiscountCoupons()->whereHas('project', function($query) use ($projectId) {
                    $query->where('project', $projectId);
                })->get();

                return DiscountCouponsResource::collection($coupons);
            } else {
                return response()->json('Projeto não encontrado');
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar cupons (DiscountCouponsController - index)');
            report($e);
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

            $requestData            = $request->validated();
            $requestData["project"] = Hashids::decode($requestData['project'])[0];
            $discountCouponSaved    = $this->getDiscountCoupons()->create($requestData);
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
            $data = $request->all();
            if (isset($data['couponId'])) {
                $couponId = Hashids::decode($data['couponId'])[0];
                $coupon   = $this->getDiscountCoupons()->find($couponId);
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
            $data = $request->all();
            if (isset($data['couponId'])) {
                $couponId = Hashids::decode($data['couponId'])[0];
                $coupon   = $this->getDiscountCoupons()->find($couponId);
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
            $requestValidated = $request->validated();
            $couponId         = Hashids::decode($id)[0];
            $coupon           = $this->getDiscountCoupons()->find($couponId);
            $couponUpdated    = $coupon->update($requestValidated);
            if ($couponUpdated) {
                return response()->json('Sucesso', 200);
            }
            //            $data          = $request->input('coupomData');
            //            $coupomId      = Hashids::decode($data['id'])[0];
            //            $coupom        = $this->getDiscountCoupons()->find($coupomId);
            //            $coupomUpdated = $coupom->update($data);
            //            if ($coupomUpdated) {
            //                return response()->json('Sucesso');
            //            }

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
            if (isset($id)) {
                $descountCouponId = Hashids::decode($id)[0];
                $descountCoupon   = $this->getDiscountCoupons()->find($descountCouponId)->delete();
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
