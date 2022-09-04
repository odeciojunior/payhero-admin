<?php

namespace Modules\Reports\Transformers;

use Illuminate\Support\Facades\DB;
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
        $projects = DB::select('select name from projects where id='.$this->project_id);
        return [
            'project'    => $projects[0]->name ?? '',
            'amount'     => $this->amount ?? 0,
            'cupom_code' => $this->cupom_code ?? ''
        ];
    }
}
