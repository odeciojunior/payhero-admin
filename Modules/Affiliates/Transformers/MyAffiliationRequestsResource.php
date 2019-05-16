<?php

namespace Modules\Affiliates\Transformers;

use App\Entities\Project;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class MinhasAfiliacoesSolicitacoesResource extends Resource {

    public function toArray($request) {

        $project = Project::find($this->project);

        return [
            'id'         => Hashids::encode($this->id),
            'project'    => $project['name'],
            'status'     => $this->status,
            'created_at' => $this->created_at
        ];

    }

}
