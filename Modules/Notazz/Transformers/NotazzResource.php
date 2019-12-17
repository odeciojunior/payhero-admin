<?php

namespace Modules\Notazz\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class NotazzResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $this->load('project');

        return [
            'id'              => Hashids::encode($this->id),
            'invoice_type'    => $this->invoice_type,
            'token_api'       => $this->token_api,
            'token_webhook'   => $this->token_webhook,
            'token_logistics' => $this->token_logistics,
            'start_date'      => Carbon::parse($this->start_date)
                                       ->format('d/m/Y'), //($this->start_date) ? $this->start_date->format('d/m/Y') : '',
            'project_id'      => Hashids::encode($this->project->id),
            'project_name'    => substr($this->project->name, 0, 20),
            'project_photo'   => $this->project->photo,
            'pending_days'    => $this->pending_days,
            'remove_tax'      => $this->discount_plataform_tax_flag,
            'emit_zero'       => $this->generate_zero_invoice_flag,
            'created_at'      => $this->created_at->format('d/m/Y'),
        ];
    }
}
