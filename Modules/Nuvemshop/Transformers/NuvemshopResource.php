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

        $authorizationUrl =
            "https://" . $integration->url_store . "/admin/apps/" . env("NUVEMSHOP_CLIENT_ID") . "/authorize";

        return [
            "id" => Hashids::encode($integration->id),
            "project_id" => Hashids::encode($this->id),
            "project_name" => substr($this->name, 0, 25),
            "project_photo" => $this->photo,
            "authorization_url" => $authorizationUrl,
            "status" => $integration->status,
            "token" => $integration->token,
            "created_at" => $this->created_at->format("d/m/Y"),
        ];
    }
}
