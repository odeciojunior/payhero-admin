<?php

namespace Modules\Withdrawals\Transformers;

use Illuminate\Http\Resources\Json\Resource;

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
            'date_release'        => $this->release_date->format('d/m/Y'),
            'value'               => $this->value,
            'status'              => $this->status,
        ];

    }


}
