<?php

namespace Modules\Projects\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed name
 */
class ProjectsSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'      => Hashids::encode($this->id),
            'name'    => $this->name,
            'shopify' => ($this->shopify_id != null ? 1 : 0),
            'status'  => $this->status,
            'woocommerce' => ($this->woocommerce_id != null ? 1 : 0),
        ];
    }
}
