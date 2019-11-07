<?php

namespace Modules\Trackings\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class TrackingResource extends Resource
{
    public function toArray($request)
    {
        if($this->tracking){
            return [
                'id' => Hashids::encode($this->tracking->id),
                'tracking_code' => $this->tracking->tracking_code,
                'tracking_status_enum' => $this->tracking->tracking_status_enum,
                'tracking_status' => $this->tracking->tracking_status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $this->present()->getTrackingStatusEnum($this->tracking->tracking_status_enum)) : 'Não informado',
                'sale' => Hashids::connection('sale_id')->encode($this->sale->id),
                'product' => [
                    'name' => $this->product->name,
                    'description' => $this->product->description,
                    'amount' =>  $this->tracking->amount,
                ]
            ];
        }else{

            $amount = '';
            //only relations earger loaded
            if($this->sale->relationLoaded('plansSales')) {
                $planSale = $this->sale
                    ->plansSales
                    ->where('plan_id', $this->plan_id)
                    ->where('sale_id', $this->sale_id)
                    ->first();

                if($planSale->relationLoaded('plan') && $planSale->plan->relationLoaded('productsPlans')) {
                    $productPlan = $planSale->plan
                        ->productsPlans
                        ->where('product_id', $this->product_id)
                        ->where('plan_id', $this->plan_id)
                        ->first();

                    if (isset($planSale) && isset($productPlan)) {
                        $amount = $planSale->amount * $productPlan->amount;
                    }
                }
            }

            return [
                'id' => '',
                'tracking_code' => '',
                'tracking_status_enum' => '',
                'tracking_status' => 'Não informado',
                'sale' => Hashids::connection('sale_id')->encode($this->sale->id),
                'product' => [
                    'name' => $this->product->name,
                    'description' => $this->product->description,
                    'amount' =>  $amount,
                ]
            ];
        }

    }
}
