<?php

namespace Modules\ProjectUpsellConfig\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\ProductPlan;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class PreviewUpsellResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        $productPlanModel = new ProductPlan();
        $offerPlanDecoded = json_decode($this->offer_on_plans);
        $productArray     = [];

        $upsellPlans = $productPlanModel->with(['product', 'plan'])
                                        ->whereIn('plan_id', $offerPlanDecoded)
                                        ->get();
        foreach ($upsellPlans as $upsellPlan) {
            $productArray[] = [
                'name'        => $upsellPlan->product->name,
                'description' => $upsellPlan->product->description,
                'amount'      => $upsellPlan->amount,
                'photo'       => $upsellPlan->product->photo,
                'price'       => $upsellPlan->plan->price,
            ];
        }

        return [
            'id'             => Hashids::encode($this->id),
            'header'         => $this->header,
            'title'          => $this->title,
            'description'    => $this->description,
            'countdown_time' => $this->countdown_time ?? '',
            'countdown_flag' => $this->countdown_flag,
            'products'       => $productArray,
        ];
    }
}
