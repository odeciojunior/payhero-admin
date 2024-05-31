<?php

namespace Modules\Utmify\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class UtmifyIntegrationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "user_id" => Hashids::encode($this->user_id),
            "project_id" => Hashids::encode($this->project_id),
            "project_name" => $this->project->name,
            "project_photo" => $this->project->project_photo,
            "token" => $this->token ?? null,
            "created_at" => $this->created_at->format("d/m/Y"),
            "updated_at" => $this->created_at->format("d/m/Y"),
        ];
    }
}
