<?php

namespace Modules\Affiliates\Transformers;

use Modules\Core\Entities\User;
use Modules\Core\Entities\Project;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class MeusAffiliatesResource extends Resource {

    public function toArray($request) {

        $affiliate = User::find($this->user);
        $project   = Project::find($this->project);

        return [
            'id'          => Hashids::encode($this->id),
            'affiliate'   => $affiliate['name'],
            'projeto'     => $project['name'],
            'percentage'  => $this->percentage,
            'created_at'  => $this->created_at
        ];
    }

}
