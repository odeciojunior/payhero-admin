<?php

namespace Modules\Withdrawals\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

class WithdrawalResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {

        return [
            'id'                  => Hashids::encode($this->id),
            'account_information' => $this->account_information,
            'date_request'        => $this->created_at->format('d/m/Y'),
            'date_release'        => isset($this->release_date) ? $this->release_date->format('d/m/Y') : '',
            'value'               => 'R$ '.number_format(intval($this->value) / 100, 2, ',', '.'),
            'status'              => $this->status,
        ];

    }


}
