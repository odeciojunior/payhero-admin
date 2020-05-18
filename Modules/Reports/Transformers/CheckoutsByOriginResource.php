<?php

namespace Modules\Reports\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutsByOriginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'origin'       => $this->origin ?? 0,
            'qtd_checkout' => $this->qtd_checkout ?? 0,
        ];
    }
}
