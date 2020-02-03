<?php

namespace Modules\ActiveCampaign\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ActivecampaignResource extends Resource
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
            'id'                  => Hashids::encode($this->id),
            'project_id'          => Hashids::encode($this->project->id),
            'project_name'        => substr($this->project->name, 0, 20),
            'project_photo'       => $this->project->photo,
            'project_description' => $this->project->description,
            'api_url'             => $this->api_url,
            'api_key'             => $this->api_key,
            'created_at'          => $this->created_at->format('d/m/Y'),
        ];
    }
}
