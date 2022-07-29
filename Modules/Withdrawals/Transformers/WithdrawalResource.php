<?php

namespace Modules\Withdrawals\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Modules\Core\Services\BankService;
use Vinkla\Hashids\Facades\Hashids;

class WithdrawalResource extends JsonResource
{
    public function toArray($request): array
    {
        $bankName = Str::title((new BankService())->getBankName($this->bank));
        $accountInformation = $this->accountInformation();

        $realeaseDate = '';
        $realeaseTime = '';
        if (!empty($this->release_date)) {
            $realeaseDate = $this->release_date->format('d/m/Y');
            $realeaseTime = $this->release_date->format('H:i');
        }

        $data = [
            'id' => Hashids::encode($this->id),
            'account_information_bank' => $bankName,
            'account_information' => $accountInformation,
            'date_request' => $this->created_at->format('d/m/Y'),
            'date_request_time' => $this->created_at->format('H:i'),
            'date_release' => $realeaseDate,
            'date_release_time' => $realeaseTime,
            'value' => number_format(intval($this->value) / 100, 2, ',', '.'),
            'status' => $this->status,
            'status_translated' => Lang::get(
                'definitions.enum.withdrawals.status.' . $this->present()
                    ->getStatus($this->status)
            ),
            'tax_value' => $this->tax,
            'debt_pending_value' => 'R$' . number_format(intval($this->debt_pending_value) / 100, 2, ',', '.')
        ];

        if (request('is_mobile')) {
            $data['request_datetime'] = $this->created_at->format('Y-m-d H:i:s');
            $data['release_datetime'] = $this->release_date->format('Y-m-d H:i:s');
        }

        return $data;
    }

    private function accountInformation(): string
    {
        $bankAccount =  $this->company->getDefaultBankAccount();
        if (!empty($bankAccount)) {
            switch ($bankAccount->transfer_type) {
                case 'PIX':
                    return $bankAccount->transfer_type . ': ' . $bankAccount->key_pix;
                case 'TED':
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
        return '';
    }
}
