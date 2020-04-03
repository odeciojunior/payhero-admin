<?php

namespace Modules\Trackings\Transformers;

use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class TrackingResource extends Resource
{
    public function toArray($request)
    {
        if ($this->tracking) {
            return [
                'id' => Hashids::encode($this->tracking->id),
                'pps_id' => Hashids::encode($this->id),
                'tracking_code' => $this->tracking->tracking_code,
                'tracking_status_enum' => $this->tracking->tracking_status_enum,
                'tracking_status' => $this->tracking->tracking_status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $this->tracking->present()->getTrackingStatusEnum($this->tracking->tracking_status_enum)) : 'Não informado',
                'sale' => Hashids::connection('sale_id')->encode($this->sale->id),
                'approved_date' => Carbon::parse($this->sale->end_date)->format('d/m/Y'),
                'product' => [
                    'id' => Hashids::encode($this->product->id),
                    'name' => $this->product->name,
                    'description' => $this->product->description,
                    'amount' => $this->tracking->amount,
                ]
            ];
        } else {
            return [
                'id' => '',
                'pps_id' => Hashids::encode($this->id),
                'tracking_code' => '',
                'tracking_status_enum' => '',
                'tracking_status' => 'Não informado',
                'sale' => Hashids::connection('sale_id')->encode($this->sale->id),
                'approved_date' => Carbon::parse($this->sale->end_date)->format('d/m/Y'),
                'product' => [
                    'id' => Hashids::encode($this->product->id),
                    'name' => $this->product->name,
                    'description' => $this->product->description,
                    'amount' => $this->amount ?? '',
                ]
            ];
        }

    }
}
