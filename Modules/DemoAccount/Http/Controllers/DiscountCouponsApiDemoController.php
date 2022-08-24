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

    public function edit($projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {                

                $coupon  = DiscountCoupon::find(current(Hashids::decode($id)));
                
                if ($coupon)
                {
                    $coupon->makeHidden(['id', 'project_id']);
                    
                    $coupon->rule_value = number_format($coupon->rule_value / 100, 2, ',', '.');
                    if($coupon->type==1)
                    {
                        $coupon->value = number_format($coupon->value / 100, 2, ',', '.');
                    }

                    $expires = '';
                    if(!empty($coupon->expires)){
                        $coupon->expires_date = date('d/m/Y',strtotime($coupon->expires));
                        $now = strtotime(date('Y-m-d'));
                        $_date = strtotime($coupon->expires);
                        
                        $datediff = $_date - $now;
                        $expires = round($datediff / (60 * 60 * 24));
                        $coupon->expires_days = $expires;
                        if($expires>=0){
                            $coupon->expires = 'Vence em '.$expires.' dia'.($expires>1?'s':'');
                        }else{
                            $coupon->expires = '';
                            $coupon->status = 0;
                        }
                    }

                    if(empty($coupon->plans)) $coupon->plans = '[]';
                    
                    return response()->json($coupon, 200);
                } 

                return response()->json(['message' => 'Erro ao atualizar registro'], 400);                                
            }

            return response()->json(['message' => 'Erro ao buscar Cupom'], 400);

        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao atualizar registro'], 400);
        }
    }
}
