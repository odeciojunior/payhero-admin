<?php

namespace Modules\Api\Transformers\V1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\BankService;

class WithdrawalsResumeApiResource extends JsonResource
{
    public function toArray($request)
    {
        $date = Carbon::parse($this->created_at, "America/Sao_Paulo");
        $date->setLocale("pt_BR");

        $dateWithdrawal = $date->diffForHumans();
        if ($date->diffInDays() > Carbon::DAYS_PER_WEEK) {
            $dateWithdrawal = "Em {$this->created_at->format("d/m/Y")}";
        }

        $bank = (new BankService())->getBankName($this->bank);

        return [
            "withdrawal_id" => hashids_encode($this->id),
            "gateway_name" => $this->gateway->present()->getName(),
            "bank_name" => $bank ?? "",
            "date" => $dateWithdrawal,
            "value" => foxutils()->formatMoney($this->value / 100),
            "status" => Lang::get("definitions.enum.withdrawals.status." . $this->present()->getStatus($this->status)),
        ];
    }
}
