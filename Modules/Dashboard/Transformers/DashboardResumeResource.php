<?php

namespace Modules\Dashboard\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class DashboardResumeResource
 * @package Modules\Dashboard\Transformers
 */
class DashboardResumeResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id_code'           => Hashids::encode($this->resource->company_id),
            "fantasy_name"      => $this->resource->fantasy_name,
            'today_balance'     => number_format(intval($this->resource->today_balance) / 100, 2, ',', '.'),
            'pending_balance'   => number_format(intval($this->resource->pending_balance) / 100, 2, ',', '.'),
            'available_balance' => number_format(intval($this->resource->available_balance) / 100, 2, ',', '.'),
            'total_balance'     => number_format(intval($this->resource->total_balance) / 100, 2, ',', '.'),
            'currency'          => $this->resource->country == 'usa' ? '$' : 'R$',
        ];
    }
}
