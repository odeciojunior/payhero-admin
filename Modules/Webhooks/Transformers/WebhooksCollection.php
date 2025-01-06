<?php

declare(strict_types=1);

namespace Modules\Webhooks\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WebhooksCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        $webhooks = WebhooksResource::collection($this->collection)->toArray(
            $request
        );

        return [
            "data" => $webhooks,
            "resume" => $this->when((bool)$request->get("resume", false), [
                "total" => count($webhooks),
                "received" => 0,
                "sent" => 0,
            ]),
            "links" => [
                "self" => "link-value",
            ],
        ];
    }
}
