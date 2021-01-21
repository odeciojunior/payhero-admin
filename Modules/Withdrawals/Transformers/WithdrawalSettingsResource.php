<?php

namespace Modules\Withdrawals\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Vinkla\Hashids\Facades\Hashids;

class WithdrawalSettingsResource extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'id'         => Hashids::encode($this->id),
            'company_id' => Hashids::encode($this->company->id),
            'rule'       => $this->rule,
            'frequency'  => $this->frequency,
            'weekday'    => $this->weekday,
            'day'        => $this->day,
            'amount'     => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

