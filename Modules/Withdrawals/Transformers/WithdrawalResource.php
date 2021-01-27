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
        $agency = 'Agência: ' . $this->agency . ' - Digito: ' . $this->agency_digit;
        $account = ' - Conta: ' . $this->account . ' - Digito: ' . $this->account_digit;

        $realeaseDate = '';
        if (!empty($this->release_date)){
            $realeaseDate = $this->release_date->format('d/m/Y');
        }

        return [
            'id' => Hashids::encode($this->id),
            'account_information_bank' => $bankName,
            'account_information' => $agency . $account,
            'date_request' =>$this->created_at->format('d/m/Y'),
            'date_request_time' =>$this->created_at->format('d/m/Y'),
            'date_release' => $realeaseDate,
            'date_release_time' => $realeaseDate,
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

