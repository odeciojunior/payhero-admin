<?php

namespace Modules\Trackings\Transformers;

use Illuminate\Support\Carbon;
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
                'tracking_status' => $this->tracking->tracking_status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $this->tracking->present()->getTrackingStatusEnum($this->tracking->tracking_status_enum)) : 'Não informado',
                'sale' => Hashids::connection('sale_id')->encode($this->sale->id),
                'approved_date' => Carbon::parse($this->sale->end_date)->format('d/m/Y'),
                'product' => [
                    'id' => Hashids::encode($this->product->id),
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

                if (isset($planSale) && $planSale->relationLoaded('plan')) {
                    $plan = $planSale->plan;
                    if (isset($plan) && $plan->relationLoaded('productsPlans')) {
                        $productPlan = $plan->productsPlans
                            ->where('product_id', $this->product_id)
                            ->where('plan_id', $this->plan_id)
                            ->first();

                        if (isset($productPlan)) {
                            $amount = $planSale->amount * $productPlan->amount;
                        }
                    }
                }
            }

            return [
                'id' => '',
                'tracking_code' => '',
                'tracking_status_enum' => '',
                'tracking_status' => 'Não informado',
                'sale' => Hashids::connection('sale_id')->encode($this->sale->id),
                'approved_date' => Carbon::parse($this->sale->end_date)->format('d/m/Y'),
                'product' => [
                    'id' => Hashids::encode($this->product->id),
                    'name' => $this->product->name,
                    'description' => $this->product->description,
                    'amount' =>  $amount,
                ]
            ];
        }

    }
}
