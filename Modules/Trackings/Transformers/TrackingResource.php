<?php

namespace Modules\Trackings\Transformers;

use Illuminate\Support\Carbon;
use Modules\Core\Entities\Tracking;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingResource extends JsonResource
{
    public function toArray($request)
    {
        $common = [
            'pps_id' => hashids_encode($this->id),
            'sale' => hashids_encode($this->sale_id, 'sale_id'),
            'is_chargeback_recovered' => $this->is_chargeback_recovered,
            'approved_date' => Carbon::parse($this->approved_date)->format('d/m/Y'),
            'product' => [
                'id' => $this->product_id,
                'name' => $this->product_name,
                'description' => $this->product_description ?? '',
                'amount' => $this->product_amount ?? '',
            ]
        ];

        if ($this->tracking_id) {
            $trackingPresenter = (new Tracking())->present();
            $trackingCode = $this->tracking_code == "CLOUDFOX000XX" ? '' : $this->tracking_code;

            return array_merge($common, [
                'id' => hashids_encode($this->tracking_id),
                'tracking_code' => $trackingCode,
                'tracking_status_enum' => $this->tracking_status_enum,
                'tracking_status' => $this->tracking_status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $trackingPresenter->getTrackingStatusEnum($this->tracking_status_enum)) : 'Não Informado',
                'system_status_enum' => $this->system_status_enum,
            ]);
        } else {
            return array_merge($common, [
                'id' => '',
                'tracking_code' => '',
                'tracking_status_enum' => '',
                'tracking_status' => 'Não informado',
                'system_status_enum' => '',
            ]);
        }
    }
}
