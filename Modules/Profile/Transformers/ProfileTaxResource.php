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
            'percentage_rate'                     => $this->percentage_rate,
            'transaction_rate'                    => $this->present()->getTransactionRate(),
            'release_money_days'                  => $this->release_money_days,
            'percentage_antecipable'              => $this->percentage_antecipable,
            'antecipation_tax'                    => $this->antecipation_tax,
            'boleto_antecipation_money_days'      => $this->boleto_antecipation_money_days,
            'credit_card_antecipation_money_days' => $this->credit_card_antecipation_money_days,
            'installment_tax'                     => $this->installment_tax . ' %',
        ];
    }
}
