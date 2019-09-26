<?php

namespace Modules\Projects\Transformers;

use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ProjectsResource extends Resource {

    public function toArray($request) {

        return [
            'id' => Hashids::encode($this->id),
            'photo' => $this->photo,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => (new Carbon($this->created_at))->format('d/m/Y')
        ];
    }
}
