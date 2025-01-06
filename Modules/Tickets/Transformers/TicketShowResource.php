<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Support\Carbon;
use Modules\Core\Entities\TicketAttachment;
use Modules\Core\Entities\TicketMessage;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketShowResource extends JsonResource
{
    public function toArray($request)
    {
        if (!isset($this->messages)) {
            return null;
        }
        
        $messages = $this->messages
            ->merge($this->attachments)
            ->sort(function ($a, $b) {
                return strtotime($a->created_at) > strtotime($b->created_at);
            })
            ->map(function ($item) {
                return $item instanceof TicketAttachment
                    ? new TicketAttachmentResource($item)
                    : new TicketMessageResource($item);
            })
            ->values()
            ->toArray();

        $description = (object) [
            "content" => $this->description,
            "type" => "text",
            "created_at" => \Carbon\Carbon::parse($this->created_at)->format("d/m \à\s H\hi"),
            "from" => TicketMessage::TYPE_FROM_CUSTOMER,
        ];

        array_unshift($messages, $description);

        return [
            "id" => Hashids::encode($this->id),
            "sale_id" => hashids_encode($this->sale_id, "sale_id"),
            "ticket_status_enum" => $this->ticket_status_enum,
            "ticket_category_enum" => $this->ticket_category_enum,
            "created_at" => Carbon::parse($this->created_at)->format("d/m/Y"),
            "project_name" => $this->project_name,
            "customer_name" => $this->customer_name,
            "admin_answered" => $this->messages->where("type_enum", TicketMessage::TYPE_FROM_ADMIN)->count() > 0,
            "messages" => $messages,
        ];
    }
}
