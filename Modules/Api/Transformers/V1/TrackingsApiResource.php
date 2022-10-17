<?php

namespace Modules\Api\Transformers\V1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsApiResource extends JsonResource
{
    public function toArray($request)
    {
        $trackingModel = new Tracking();

        if ($this->checkpoints) {
            $trackingService = new TrackingService();

            $tracking = Tracking::find($this->id);

            $apiTracking = $trackingService->findTrackingApi($tracking);

            $postedStatus = $tracking->present()->getTrackingStatusEnum("posted");
            $checkpoints = collect();

            //objeto postado
            $checkpoints->add([
                "tracking_status_enum" => $postedStatus,
                "tracking_status" => __(
                    "definitions.enum.tracking.tracking_status_enum." .
                        $tracking->present()->getTrackingStatusEnum($postedStatus)
                ),
                "created_at" => Carbon::parse($tracking->created_at)->format("d/m/Y"),
                "event" => "Objeto postado. As informações de rastreio serão atualizadas nos próximos dias.",
            ]);

            $checkpointsApi = $trackingService->getCheckpointsApi($tracking, $apiTracking);

            $checkpoints = $checkpoints
                ->merge($checkpointsApi)
                ->unique()
                ->sortKeysDesc()
                ->values()
                ->toArray();

            return [
                "id" => Hashids::encode($this->id),
                "tracking_code" => $this->tracking_code,
                "tracking_status_enum" => $this->tracking_status_enum,
                "checkpoints" => $checkpoints
            ];
        }

        return [
            "id" => Hashids::encode($this->id),
            "tracking_code" => $this->tracking_code,
            "tracking_status" => Lang::get(
                "definitions.enum.tracking.tracking_status_enum." .$trackingModel->present()->getTrackingStatusEnum($this->tracking_status_enum)
            ),
            "tracking_status_enum" => $this->tracking_status_enum
        ];
    }
}
