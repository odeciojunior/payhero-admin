<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class TicketResource extends Resource
{
    public function toArray($request)
    {
        $createdAt = Carbon::parse($this->created_at)->format('d/m/Y H:i:s');
        $lastMessage = $this->messages->count()
                     ? $this->messages->last()->created_at->format('d/m/Y H:i:s')
                     : $createdAt;

        return [
            'id' => Hashids::encode($this->id),
            'subject' => $this->subject,
            'description' => $this->description,
            'ticket_category_enum' => $this->ticket_category_enum,
            'ticket_category' => __('definitions.enum.ticket.category.' . $this->present()->getTicketCategoryEnum()),
            'ticket_status_enum' => $this->ticket_status_enum,
            'ticket_status' => __('definitions.enum.ticket.status.' . $this->present()->getTicketStatusEnum()),
            'created_at' => $createdAt,
            'last_message' => $lastMessage,
        ];
    }
}
