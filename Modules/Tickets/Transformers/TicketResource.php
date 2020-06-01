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
        $lastMessage = $this->messages->count()
            ? $this->messages->last()->created_at->format('d/m/Y H:i:s')
            : $createdAt;
        $this->sale;
        $userProject     = UserProject::with('company')->where('project_id', $this->sale->project_id)->first();
        $adminAnswered   = $this->messages->where('from_admin', true);
        $lastMessageFrom = '';
        if (count($this->messages) > 0) {
            $message = $this->messages->sortByDesc('id')->first();
            if ($message->from_admin) {
                $lastMessageFrom = 'admin';
            } else if ($message->from_system) {
                $lastMessageFrom = 'system';
            } else {
                $lastMessageFrom = 'customer';
            }
        }

        return [
            'id'                   => Hashids::encode($this->id),
            'subject'              => $this->subject,
            'description'          => $this->description,
            'ticket_category_enum' => $this->ticket_category_enum,
            'ticket_category'      => __('definitions.enum.ticket.category.' . $this->present()
                                                                                    ->getTicketCategoryEnum()),
            'ticket_status_enum'   => $this->ticket_status_enum,
            'ticket_status'        => __('definitions.enum.ticket.status.' . $this->present()
                                                                                  ->getTicketStatusEnum()),
            'created_at'           => $createdAt,
            'last_message'         => $lastMessage,
            'customer_name'        => $this->customer->name,
            'company_name'         => $userProject->company->fantasy_name,
            'last_message_from'    => $lastMessageFrom,
            'admin_answered'       => count($adminAnswered) > 0,
        ];
    }
}
