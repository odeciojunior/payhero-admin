<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Project;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Modules\DiscountCoupons\Transformers\DiscountCouponsResource;
use Modules\DiscountCoupons\Http\Controllers\DiscountCouponsApiController;

class DiscountCouponsApiDemoController extends DiscountCouponsApiController
{
    public function index(Request $request, $projectId)
    {
        try {
            
            $discountCouponsModel = new DiscountCoupon();            

            if (empty($projectId)) {
                return response()->json([
                    'message' => 'Erro ao listar dados de cupons',
                ], 400);
            }
            
            $projectId = current(Hashids::decode($projectId));

            $coupons   = $discountCouponsModel->whereHas('project', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            });

            if(!empty($request['name'])){                
                $coupons = $coupons->where('name', 'like', '%'.$request['name'].'%')
                ->whereOr('code', 'like', '%'.$request['name'].'%');
            }

            return DiscountCouponsResource::collection($coupons->orderBy('id', 'DESC')->paginate(5));
                          
        } catch (Exception $e) {
            
            report($e);
            return response()->json([
                'message' => 'Erro ao listar dados de cupons',
            ], 400);
        }
    }
}
