<?php

namespace Modules\Reports\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;

class PendingBalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->sale_id ? Hashids::connection('sale_id')->encode($this->sale_id) : '',
            'sale_code' => $this->sale_id ? '#' . Hashids::connection('sale_id')->encode($this->sale_id) : '',
            'project' => $this->name ?? '',
            'value' => $this->value ? FoxUtils::formatMoney($this->total_paid_value) : '',

            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->format('d/m/Y H:i:s') : '',
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->format('d/m/Y H:i:s') : '',
            'brand' => $this->flag ?? '',
        ];
    }
}
