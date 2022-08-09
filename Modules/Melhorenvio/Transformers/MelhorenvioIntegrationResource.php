<?php

namespace Modules\Melhorenvio\Transformers;

use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class MelhorenvioIntegrationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "user_id" => Hashids::encode($this->user_id),
            "name" => $this->name,
            "completed" => $this->completed,
            "created_at" => Carbon::parse($this->created_at)->format("d/m/Y"),
        ];
    }
}
