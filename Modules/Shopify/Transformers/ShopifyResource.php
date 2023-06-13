<?php

namespace Modules\Shopify\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\ShopifyIntegration;

class ShopifyResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        $integration = ShopifyIntegration::where("project_id", $this->id)->first();

        return [
            "id" => Hashids::encode($this->id),
            //'project_id' => Hashids::encode($this->project->id),
            "project_name" => substr($this->name, 0, 25),
            "project_photo" => $this->photo,
            "created_at" => $this->created_at->format("d/m/Y"),
            // "skip_to_cart" => $this->skip_to_cart,
            "token" => $integration->token,
            "skip_to_cart" => $integration->skip_to_cart,
        ];
    }
}
