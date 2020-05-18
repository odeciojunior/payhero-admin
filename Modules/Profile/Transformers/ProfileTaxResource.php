<?php

namespace Modules\Profile\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileTaxResource extends JsonResource
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
            'abroad_transfer_tax'       => $this->abroad_transfer_tax,
            'antecipation_enabled_flag' => $this->antecipation_enabled_flag,
            'antecipation_tax'          => $this->antecipation_tax,
            'percentage_antecipable'    => $this->percentage_antecipable,
        ];
    }
}
