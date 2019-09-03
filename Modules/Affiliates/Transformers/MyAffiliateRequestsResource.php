<?php

namespace Modules\Affiliates\Transformers;

use Modules\Core\Entities\User;
use Modules\Core\Entities\Project;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class MyAffiliateRequestsResource extends Resource {

    public function toArray($request) {

        $affiliate = User::find($this->user);
        $project   = Project::find($this->project);

        return [
            'id'         => Hashids::encode($this->id),
            'affiliate'  => $affiliate['name'],
            'project'    => $project['name'],
            'percentage' => $project['percentage'],
            'created_at' => $this->created_at
        ];

    }

}
