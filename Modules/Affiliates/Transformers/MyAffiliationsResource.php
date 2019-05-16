<?php

namespace Modules\Affiliates\Transformers;

use App\Entities\Project;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class MyAffiliationsResource extends Resource {

    public function toArray($request) {

        $project = Project::find($this->project);

        return [
            'id'            => Hashids::encode($this->id),
            'project_photo' => $project['photo'],
            'project_name'  => $project['name'],
        ];
    }

}
