<?php

namespace Modules\Withdrawals\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\BankService;
use Vinkla\Hashids\Facades\Hashids;

class WithdrawalResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $bankService = new BankService();

        $accountName = $bankService->getBankName($this->bank);

        return [
            'id'                  => Hashids::encode($this->id),
            'account_information' => $accountName . ' - Agência: ' . $this->agency . ' - Digito: ' . $this->agency_digit . ' - Conta: ' . $this->account . ' - Digito: ' . $this->account_digit,
            'date_request'        => $this->created_at->format('d/m/Y'),
            'date_release'        => isset($this->release_date) ? date("d/m/Y", strtotime($this->release_date)) : '',
            'value'               => 'R$ ' . number_format(intval($this->value) / 100, 2, ',', '.'),
            'status'              => $this->status,
            'status_translated'   => Lang::get('definitions.enum.withdrawals.status.' . $this->present()->getStatus($this->status)), 
        ];
    }
}
