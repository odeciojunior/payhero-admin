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
            return [
                'id' => '',
                'tracking_code' => '',
                'tracking_status_enum' => '',
                'tracking_status' => 'Não informado',
                'sale' => Hashids::connection('sale_id')->encode($this->sale->id),
                'product' => [
                    'name' => $this->product->name,
                    'description' => $this->product->description,
                    'amount' =>  '',
                ]
            ];
        }

    }
}
