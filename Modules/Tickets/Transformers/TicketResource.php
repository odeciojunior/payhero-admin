<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Support\Carbon;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProductService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class TicketResource extends Resource
{
    public function toArray($request)
    {
        $createdAt   = Carbon::parse($this->created_at)->format('d/m/Y H:i:s');
        $lastMessage = $this->messages->count()
            ? $this->messages->last()->created_at->format('d/m/Y H:i:s')
            : $createdAt;
        $this->sale;
        $userProject = UserProject::with('company')->where('project_id', $this->sale->project_id)->first();

        return [
            'id'                   => Hashids::encode($this->id),
            'subject'              => $this->subject,
            'description'          => $this->description,
            'ticket_category_enum' => $this->ticket_category_enum,
            'ticket_category'      => __('definitions.enum.ticket.category.' . $this->present()
                                                                                    ->getTicketCategoryEnum()),
            'ticket_status_enum'   => $this->ticket_status_enum,
            'ticket_status'        => __('definitions.enum.ticket.status.' . $this->present()->getTicketStatusEnum()),
            'created_at'           => $createdAt,
            'last_message'         => $lastMessage,
            'customer_name'        => $this->customer->name,
            'company_name'         => $userProject->company->fantasy_name,
        ];
    }
}
