<?php

namespace Modules\Api\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\Tracking;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsApiResource extends JsonResource
{
    public function toArray($request)
    {
        $trackingModel = new Tracking();

        return [
            "id" => Hashids::encode($this->id),
            "code" => $this->tracking_code,
            "status" => Lang::get(
                "definitions.enum.tracking.tracking_status_enum." .$trackingModel->present()->getTrackingStatusEnum($this->tracking_status_enum)
            ),
            "status_enum" => $this->tracking_status_enum
        ];
    }
}
