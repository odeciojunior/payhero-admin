<?php

namespace Modules\Smartfunnel\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class SmartfunnelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "project_id" => Hashids::encode($this->project->id),
            "project_name" => mb_substr($this->project->name, 0, 20),
            "project_photo" => $this->project->photo,
            "api_url" => $this->api_url,
            "created_at" => $this->created_at->format("d/m/Y"),
        ];
    }
}
