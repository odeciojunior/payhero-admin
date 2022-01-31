<?php

namespace Modules\Trackings\Transformers;

use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->tracking) {
            $trackingCode = $this->tracking->tracking_code == "CLOUDFOX000XX"
                ? ''
                : $this->tracking->tracking_code;
            return [
                'id' => Hashids::encode($this->tracking->id),
                'pps_id' => Hashids::encode($this->id),
                'tracking_code' => $trackingCode,
                'tracking_status_enum' => $this->tracking->tracking_status_enum,
                'tracking_status' => $this->tracking->tracking_status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $this->tracking->present()->getTrackingStatusEnum($this->tracking->tracking_status_enum)) : 'Não Informado',
                'system_status_enum' => $this->tracking->system_status_enum,
                'sale' => Hashids::connection('sale_id')->encode($this->sale->id),
                'is_chargeback_recovered' => $this->sale->is_chargeback_recovered,
                'approved_date' => Carbon::parse($this->sale->end_date)->format('d/m/Y'),
                'product' => [
                    'id' => !empty($this->product) ? Hashids::encode($this->product->id) : Hashids::encode($this->productSaleApi->id),
                    'name' => !empty($this->product) ? $this->product->name : $this->productSaleApi->name,
                    'description' => !empty($this->product) ? $this->product->description : '',
                    'amount' => $this->amount ?? '',
                ]
            ];
        } else {
            return [
                'id' => '',
                'pps_id' => Hashids::encode($this->id),
                'tracking_code' => '',
                'tracking_status_enum' => '',
                'tracking_status' => 'Não informado',
                'system_status_enum' => '',
                'sale' => Hashids::connection('sale_id')->encode($this->sale->id),
                'is_chargeback_recovered' => $this->sale->is_chargeback_recovered,
                'approved_date' => Carbon::parse($this->sale->end_date)->format('d/m/Y'),
                'product' => [
                    'id' => !empty($this->product) ? Hashids::encode($this->product->id) : Hashids::encode($this->productSaleApi->id),
                    'name' => !empty($this->product) ? $this->product->name : $this->productSaleApi->name,
                    'description' => !empty($this->product) ? $this->product->description : '',
                    'amount' => $this->amount ?? '',
                ]
            ];
        }

    }
}
