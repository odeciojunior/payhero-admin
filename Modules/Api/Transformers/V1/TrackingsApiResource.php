<?php

namespace Modules\Api\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\Tracking;

class TrackingsApiResource extends JsonResource
{
    public function toArray($request)
    {
        $trackingModel = new Tracking();

        if (!empty($this->checkpoints)) {
            return [
                "id" => hashids_encode($this->id),
                "tracking_code" => $this->tracking_code,
                "tracking_status" => Lang::get(
                    "definitions.enum.tracking.tracking_status_enum." .$trackingModel->present()->getTrackingStatusEnum($this->tracking_status_enum)
                ),
                "tracking_status_enum" => $this->tracking_status_enum,
                "checkpoints" => $this->checkpoints,
                "product" => [
                    'id' => hashids_encode($this->product_id),
                    'name' => $this->product_name,
                    'description' => $this->product_description,
                    'amount' => $this->product_amount
                ]
            ];
        }

        return [
            "id" => hashids_encode($this->id),
            "tracking_code" => $this->tracking_code,
            "tracking_status" => Lang::get(
                "definitions.enum.tracking.tracking_status_enum." .$trackingModel->present()->getTrackingStatusEnum($this->tracking_status_enum)
            ),
            "tracking_status_enum" => $this->tracking_status_enum,
            "product" => [
                'id' => hashids_encode($this->product_id),
                'name' => $this->product_name,
                'description' => $this->product_description,
                'amount' => $this->product_amount
            ]
        ];
    }
}
