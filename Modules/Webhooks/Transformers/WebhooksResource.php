<?php

namespace Modules\Webhooks\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class WebhooksResource
 * @package Modules\Webhooks\Transformers
 */
class WebhooksResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     * @param Request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => hashids_encode($this->id),
            "url" => $this->url,
            "description" => $this->description,
            "register_date" => $this->created_at->format("d/m/Y"),
            "company_id" => hashids_encode($this->company->id) ?? "",
            "company_name" => $this->company->fantasy_name ?? "",
        ];
    }
}
