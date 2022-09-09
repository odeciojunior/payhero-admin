<?php

namespace Modules\Api\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Modules\Core\Services\BankService;

class WithdrawalsApiResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "withdrawal_id" => hashids_encode($this->id),
            "gateway_name" => $this->gateway->present()->getName(),
            "account_information_bank" => Str::title((new BankService())->getBankName($this->bank)),
            "account_information" => $this->accountInformation(),
            "value" => foxutils()->formatMoney($this->value / 100),
            "tax_value" => foxutils()->formatMoney($this->tax / 100),
            "debt_pending_value" => foxutils()->formatMoney($this->debt_pending_value / 100),
            "request_date" => $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : "",
            "release_date" => $this->release_date ? $this->release_date->format("Y-m-d H:i:s") : "",
            "status" => Lang::get("definitions.enum.withdrawals.status." . $this->present()->getStatus($this->status)),
        ];
    }

    private function accountInformation()
    {
        $bankAccount = $this->company->getDefaultBankAccount();

        if (!empty($bankAccount)) {
            switch ($bankAccount->transfer_type) {
                case "PIX":
                    return $bankAccount->transfer_type . ": " . $bankAccount->key_pix;
                case "TED":
                    $agency = "Ag: $bankAccount->agency";

                    if ($bankAccount->agency_digit) {
                        $agency .= "-$bankAccount->agency_digit";
                    }

                    $account = "Conta: $bankAccount->account";

                    if ($bankAccount->account_digit) {
                        $account .= "-$bankAccount->account_digit";
                    }

                    return "$agency - $account";
            }
        }

        return "";
    }
}
