<?php

namespace Modules\Reports\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportCouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'project'    => $this->project_name ?? '',
            'amount'     => $this->amount ?? 0,
            'cupom_code' => $this->cupom_code ?? ''
        ];
    }
}
