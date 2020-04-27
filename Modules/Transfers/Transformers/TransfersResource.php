<?php

namespace Modules\Transfers\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;

class TransfersResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     * @throws PresenterException
     */

    public function toArray($request)
    {
        $transactionPresenter = (new Transaction())->present();

        $codeAnticipation = null;

        if (!empty($this->anticipation_id)) {
            $anticipation     = $this->anticipation->first();
            $valueAnticipated = number_format(intval($anticipation->value) / 100, 2, ',', '.');
            $codeAnticipation = Hashids::connection('anticipation_id')->encode($anticipation->id);
        } else if (!empty($this->transaction_id) && !empty($this->transaction->anticipatedTransactions()->first())) {
            $anticipatedTransaction     = $this->transaction->anticipatedTransactions()->first();
            $valueAnticipated = number_format(intval($anticipatedTransaction->value) / 100, 2, ',', '.');

            $codeAnticipation = Hashids::connection('anticipation_id')->encode($anticipatedTransaction->anticipation_id);
        } else {
            $valueAnticipated = '0,00';
        }

        if (!empty($this->transaction) && empty($this->reason)) {
            $reason = 'Transação';
        } else if (!empty($this->transaction) && $this->reason == 'chargedback') {
            $reason = 'Chargeback';
        } else if (empty($this->transaction) && $this->reason == 'chargedback') {
            $reason = 'Chargeback';
        } else if ($this->reason == 'refunded') {
            $reason = 'Estorno da transação';
        } else if ($this->reason == 'Antecipação') {
            $reason = 'Antecipação';
        } else {
            $reason = $this->reason;
        }

        $type             = $this->type_enum == 2 ? '-' : '';
        $value            = number_format(intval($type . $this->value) / 100, 2, ',', '.');
        $currency         = $this->currency == 'dolar' ? '$ ' . $value : 'R$ ';
        $value            = $currency . $value;
        $valueAnticipated = $valueAnticipated != '0,00' ? $currency . $valueAnticipated : '0,00';

        $tax = '';
        if (!empty($this->anticipation_id)) {
            $tax = $currency . number_format(intval($this->anticipation->tax) / 100, 2, ',', '.');
        }

        $isOwner = $this->transaction_type == $transactionPresenter->getType('producer') || is_null($this->transaction_type);

        $saleDate = !empty($this->transaction) ? Carbon::parse($this->transaction->sale->start_date)
                                                       ->format('d/m/Y') : '';

        return [
            'id'                => Hashids::encode($this->id),
            'type'              => $this->type,
            'type_enum'         => $this->type_enum,
            'value'             => $value,
            'reason'            => $reason,
            'sale_id'           => Hashids::connection('sale_id')->encode($this->sale_id),
            'anticipation_id'   => $codeAnticipation,
            'date'              => $this->created_at->format('d/m/Y'),
            'is_owner'          => $isOwner,
            'sale_date'         => $saleDate,
            'value_anticipable' => $valueAnticipated,
            'tax'               => $tax,
        ];
    }
}
