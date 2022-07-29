<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Usado somente para carregar os planos no select2
 */
class PlansSelect2Resource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => hashids_encode($this->id),
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
