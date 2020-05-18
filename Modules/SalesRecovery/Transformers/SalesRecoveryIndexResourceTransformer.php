<?php

namespace Modules\SalesRecovery\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesRecoveryIndexResourceTransformer extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!empty($this->id_code)) {
            return [
                'id'   => $this->id_code,
                'name' => $this->name,
            ];
        }
    }
}
