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
            'id'            => Hashids::encode($this->id),
            'message'       => $this->message,
            'type' => $this->present()->getType(),
            'type_enum' => $this->type_enum,
            'created_at'    => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            'admin_name'    => auth()->user()->name,
        ];
    }
}
