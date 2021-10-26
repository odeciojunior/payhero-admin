<?php

namespace Modules\Withdrawals\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\BankService;

class WithdrawalsResumeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'gateway_name' => $this->gateway->present()->getName(),
            'bank_name' => (new BankService())->getBankName($this->bank),
            'value' => foxutils()->formatMoney($this->value / 100),
            'status' => $this->status,
            'status_translated' => Lang::get(
                'definitions.enum.withdrawals.status.' . $this->present()->getStatus($this->status)
            )
        ];
    }
}

