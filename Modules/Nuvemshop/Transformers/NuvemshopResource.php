<?php

namespace Modules\Nuvemshop\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\NuvemshopIntegration;
use Vinkla\Hashids\Facades\Hashids;

class NuvemshopResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        $integration = NuvemshopIntegration::where("project_id", $this->id)->first();

        return [
            "id" => Hashids::encode($this->id),
            "project_name" => substr($this->name, 0, 25),
            "project_photo" => $this->photo,
            "skip_to_cart" => $integration->skip_to_cart,
            "created_at" => $this->created_at->format("d/m/Y"),
        ];
    }
}
