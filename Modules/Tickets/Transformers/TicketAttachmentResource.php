<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class TicketAttachmentResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => Hashids::encode($this->id),
            'file' => $this->file,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
        ];
    }
}
