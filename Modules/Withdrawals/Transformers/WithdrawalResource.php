<?php

namespace Modules\Withdrawals\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\BankService;
use Vinkla\Hashids\Facades\Hashids;

class WithdrawalResource extends JsonResource
{
    public function toArray($request): array
    {
        $bankName = (new BankService())->getBankName($this->bank);
        $agency = ' - Agência: ' . $this->agency . ' - Digito: ' . $this->agency_digit;
        $account = ' - Conta: ' . $this->account . ' - Digito: ' . $this->account_digit;

        return [
            'id' => Hashids::encode($this->id),
            'account_information' => $bankName . $agency . $account,
            'date_request' => $this->created_at->format('d/m/Y H:i:s'),
            'date_release' => $this->resource->present()->getDateReleaseFormatted($this->release_date),
            'value' => 'R$ ' . number_format(intval($this->value) / 100, 2, ',', '.'),
            'status' => $this->status,
            'status_translated' => Lang::get(
                'definitions.enum.withdrawals.status.' . $this->present()
                    ->getStatus($this->status)
            ),
            'tax_value' => $this->value,
        ];
    }
}

