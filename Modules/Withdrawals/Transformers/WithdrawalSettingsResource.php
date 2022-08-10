<?php

namespace Modules\Withdrawals\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class WithdrawalSettingsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => Hashids::encode($this->id),
            "company_id" => Hashids::encode($this->company->id),
            "rule" => $this->rule,
            "frequency" => $this->frequency,
            "weekday" => $this->weekday,
            "day" => $this->day,
            "amount" => ((int) $this->amount) / 100,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
