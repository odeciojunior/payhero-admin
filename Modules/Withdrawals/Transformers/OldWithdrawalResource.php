<?php

namespace Modules\Withdrawals\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\BankService;
use Modules\Core\Services\CompanyService;
use Vinkla\Hashids\Facades\Hashids;

class OldWithdrawalResource extends JsonResource
{
    public function toArray($request)
    {
        $bankService = new BankService();

        $accountName = $bankService->getBankName($this->bank);

        if (!empty($this->value_transferred)) {
            $companyService = new CompanyService();
            $currency = $companyService->getCurrency($this->company);
            $valueTransferred = Lang::get('definitions.enum.currency.' . $currency) . ' ' . number_format(
                    intval($this->value_transferred) / 100,
                    2,
                    ',',
                    '.'
                );
        } else {
            $valueTransferred = '';
        }

        $realeaseDate = '';
        $realeaseTime = '';
        if (!empty($this->release_date)) {
            $realeaseDate = $this->release_date->format('d/m/Y');
            $realeaseTime = $this->release_date->format('H:i');
        }

        return [
            'id' => Hashids::encode($this->id),
            'account_information' => $accountName . ' - Agência: ' . $this->agency . ' - Digito: ' . $this->agency_digit . ' - Conta: ' . $this->account . ' - Digito: ' . $this->account_digit,
            'date_request' => $this->created_at->format('d/m/Y'),
            'date_request_time' => $this->created_at->format('H:i:s'),
            'date_release' => $realeaseDate,
            'date_release_time' => $realeaseTime,
            'value' => 'R$ ' . number_format(intval($this->value) / 100, 2, ',', '.'),
            'status' => $this->status,
            'status_translated' => Lang::get(
                'definitions.enum.withdrawals.status.' . $this->present()
                    ->getStatus($this->status)
            ),
            'tax_value' => $this->value,
            'value_transferred' => $valueTransferred,
        ];
    }

}
