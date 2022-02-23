<?php

namespace Modules\Withdrawals\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\BankService;

class WithdrawalsResumeResource extends JsonResource
{
    public function toArray($request): array
    {
        $date = Carbon::parse($this->created_at, 'America/Sao_Paulo');
        $date->setLocale('pt_BR');

        $dateWithdrawal =  $date->diffForHumans();
        if ($date->diffInDays() > Carbon::DAYS_PER_WEEK) {
            $dateWithdrawal = "Em {$this->created_at->format('d/m/Y')}";
        }
        $bank = (new BankService())->getBankName($this->bank);
        return [
            'gateway_name' => $this->gateway->present()->getName(),
            'bank_name' => $bank??' - ',
            'date' => $dateWithdrawal,
            'value' => foxutils()->formatMoney($this->value / 100),
            'status' => $this->status,
            'status_translated' => Lang::get(
                'definitions.enum.withdrawals.status.' . $this->present()->getStatus($this->status)
            )
        ];
    }
}

