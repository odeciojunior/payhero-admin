<?php

namespace Modules\Projects\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class UserProjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'company_id' => Hashids::encode($this->company_id),
            'project_id' => Hashids::encode($this->project_id),
            'user_id' =>  Hashids::encode($this->user_id)
        ];
    }
}
