<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "content" => $this->message,
            "type" => "text",
            "created_at" => Carbon::parse($this->created_at)->format("d/m \à\s H\hi"),
            "from" => $this->type_enum,
        ];
    }
}
