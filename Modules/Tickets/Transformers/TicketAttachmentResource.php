<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketAttachmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => Hashids::encode($this->id),
            'content' => basename($this->file),
            'type' => 'file',
            'created_at' => Carbon::parse($this->created_at)->format('d/m \à\s H\hi'),
            'link' => $this->file,
            'from' => $this->type_enum,
        ];
    }
}
