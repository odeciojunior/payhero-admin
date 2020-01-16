<?php

namespace Modules\Shopify\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ShopifyResource extends Resource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                  => Hashids::encode($this->id),
//            'project_id'          => Hashids::encode($this->project->id),
            'project_name'        => substr($this->name, 0, 20),
            'project_photo'       => $this->photo,
            'created_at'          => $this->created_at->format('d/m/Y'),
            'skip_to_cart'        => $this->skip_to_cart,
        ];
    }
}
