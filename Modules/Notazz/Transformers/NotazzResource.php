<?php

namespace Modules\Notazz\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class NotazzResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => Hashids::encode($this->id),
            'project_name'  => substr($this->project->name, 0, 20),
            'project_photo' => $this->project->photo,
            'created_at'    => $this->created_at->format('d/m/Y'),
        ];
    }
}
