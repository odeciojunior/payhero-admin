<?php

namespace Modules\Tickets\Transformers;

use Illuminate\Support\Carbon;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProductService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketShowResource extends JsonResource
{
    public function toArray($request)
    {
        $createdAt          = Carbon::parse($this->created_at)->format('d/m/Y H:i:s');
        $updatedAt          = Carbon::parse($this->updated_at)->format('d/m/Y H:i:s');
        $lastMessageDate    = Carbon::parse($this->last_message_date)->format('d/m/Y H:i:s');
        $userProject        = UserProject::with('company')->where('project_id', $this->sale->project_id)->first();
        $productService     = new ProductService();
        $products           = $productService->getTicketPlans($this->sale);

        return [
            'id'                     => Hashids::encode($this->id),
            'subject'                => $this->subject,
            'description'            => $this->description,
            'ticket_category_enum'   => $this->ticket_category_enum,
            'ticket_category'        => __('definitions.enum.ticket.category.' . $this->present()
                                                                                    ->getTicketCategoryEnum()),
            'ticket_status_enum'     => $this->ticket_status_enum,
            'ticket_status'          => __('definitions.enum.ticket.status.' . $this->present()->getTicketStatusEnum()),
            'last_message_type_enum' => $this->last_message_type_enum,
            'last_message_type'      => $this->present()->getLastMessageType(),
            'last_message_date'      => $lastMessageDate,
            'created_at'             => $createdAt,
            'updated_at'             => $updatedAt,
            'customer_name'          => $this->customer->name,
            'company_name'           => $userProject->company->fantasy_name,
            'total_paid_value'       => 'R$ ' . number_format($this->sale->total_paid_value, 2, ',', '.'),
            'sale_code'              => '#' . Hashids::connection('sale_id')->encode($this->sale->id),
            'products'               => $products,
            'attachments'            => TicketAttachmentResource::collection($this->attachments),
            'messages'               => TicketMessageResource::collection($this->messages),
            'project_name'           => $this->sale->project->name,
            'project_logo'           => $this->sale->project->logo,
        ];
    }
}
