<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Support\Carbon;
use Modules\Core\Entities\UserProject;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request)
    {
        $createdAt   = Carbon::parse($this->created_at)->format('d/m/Y H:i:s');
        $lastMessageDate    = Carbon::parse($this->last_message_date)->format('d/m/Y H:i:s');
        $userProject     = UserProject::with('company')->where('project_id', $this->sale->project_id)->first();
        $adminAnswered   = $this->messages->where('type_enum', $this->present()->getLastMessageType('from_admin'));

        return [
            'id'                     => Hashids::encode($this->id),
            'subject'                => $this->subject,
            'subject_enum'           => $this->subject_enum,
            'description'            => $this->description,
            'ticket_category_enum'   => $this->ticket_category_enum,
            'ticket_category'        => __('definitions.enum.ticket.category.' . $this->present()
                                                                                    ->getTicketCategoryEnum()),
            'ticket_status_enum'     => $this->ticket_status_enum,
            'ticket_status'          => __('definitions.enum.ticket.status.' . $this->present()
                                                                                  ->getTicketStatusEnum()),
            'last_message_type_enum' => $this->last_message_type_enum,
            'last_message_type'      => $this->present()->getLastMessageType(),
            'last_message_date'      => $lastMessageDate,
            'created_at'             => $createdAt,
            'customer_name'          => $this->customer->name,
            'company_name'           => $userProject->company->fantasy_name,
            'admin_answered'         => count($adminAnswered) > 0,
        ];
    }
}
