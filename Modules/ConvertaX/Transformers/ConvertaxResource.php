<?php

namespace Modules\ConvertaX\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ConvertaxResource extends Resource
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
            'id'            => Hashids::encode($this->id),
            'project_name'  => substr($this->project->name, 0, 20),
            'project_photo' => $this->project->photo,
            'created_at'    => $this->created_at->format('d/m/Y'),
        ];
    }
}
