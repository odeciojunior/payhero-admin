<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class TicketMessageResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id'            => Hashids::encode($this->id),
            'message'       => $this->message,
            'from_admin'    => $this->from_admin,
            'from_system'    => $this->from_system,
            'created_at'    => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            'admin_name'    => auth()->user()->name,
        ];
    }
}
