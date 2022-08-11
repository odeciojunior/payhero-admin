<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => hashids_encode($this->id),
            "subject" => $this->subject,
            "description" => $this->description,
            "ticket_status_enum" => $this->ticket_status_enum,
            "last_message_type_enum" => $this->last_message_type_enum,
            "admin_answered" => $this->admin_answers > 0,
            "customer_name" => $this->customer_name,
        ];
    }
}
