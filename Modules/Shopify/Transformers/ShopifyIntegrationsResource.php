<?php

namespace Modules\Shopify\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ShopifyIntegrationsResource extends Resource {

    public function toArray($request) {

        return [
            'id' => Hashids::encode($this->id),
            'project_id' => Hashids::encode($this->project_id),
            'user_id' => Hashids::encode($this->user_id),
            'status' => $this->status
        ];
    }
}
