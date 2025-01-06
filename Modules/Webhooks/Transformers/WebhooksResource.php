<?php

declare(strict_types=1);

namespace Modules\Webhooks\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class WebhooksResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => hashids_encode($this->id),
            'url' => $this->url,
            'description' => $this->description,
            'register_date' => $this->created_at->format('d/m/Y'),
            'company_id' => hashids_encode($this->company->id) ?? '',
            'company_name' => $this->company->fantasy_name ?? '',
            'signature' => $this->signature ?? '',
        ];
    }
}
