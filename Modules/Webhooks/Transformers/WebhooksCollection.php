<?php

namespace Modules\Webhooks\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class WebhooksCollection
 * @package Modules\Webhooks\Transformers
 */
class WebhooksCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $webhooks = WebhooksResource::collection($this->collection)->toArray(
            $request
        );

        return [
            "data" => $webhooks,
            "resume" => $this->when((bool) $request->get("resume", false), [
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
