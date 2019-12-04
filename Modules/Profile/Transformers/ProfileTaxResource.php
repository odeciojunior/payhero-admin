<?php

namespace Modules\Profile\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ProfileTaxResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'transaction_rate'          => $this->present()->getTransactionRate(),
            'credit_card_tax'           => $this->credit_card_tax,
            'debit_card_tax'            => $this->debit_card_tax,
            'credit_card_release_money' => $this->credit_card_release_money_days,
            'debit_card_release_money'  => $this->debit_card_release_money_days,
            'installment_tax'           => $this->installment_tax,
            'boleto_tax'                => $this->boleto_tax,
            'boleto_release_money'      => $this->boleto_release_money_days,
        ];
    }
}
