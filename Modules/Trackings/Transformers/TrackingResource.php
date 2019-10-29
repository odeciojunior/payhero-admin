<?php

namespace Modules\Trackings\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class TrackingResource extends Resource
{
    public function toArray($request)
    {
        return [
            'tracking_code' => $this->tracking_code,
            'tracking_status_enum' => $this->tracking_status_enum,
            'tracking_status' => $this->tracking_status_enum ? __('definitions.enum.product_plan_sale.tracking_status_enum.' . $this->present()->getTrackingStatusEnum($this->tracking_status_enum)) : 'Não informado',
            'sale' => Hashids::connection('sale_id')->encode($this->sale->id),
            'product' => [
                'name' => $this->product->name,
                'photo' => $this->product->photo,
            ]
        ];
    }
}
