<?php

namespace Modules\Transfers\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;

class TransfersResource extends JsonResource
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

        if (!empty($this->transaction) && empty($this->reason)) {
            $reason = 'Transação';
        } elseif (!empty($this->transaction) && $this->reason == 'chargedback') {
            $reason = 'Chargeback';
        } elseif (empty($this->transaction) && $this->reason == 'chargedback') {
            $reason = 'Chargeback';
        } elseif ($this->reason == 'refunded') {
            $reason = 'Estorno da transação';
        } elseif ($this->reason == 'Antecipação') {
            $reason = 'Antecipação';
        } else {
            $reason = $this->reason;
        }

        $type = $this->type_enum == 2 ? '-' : '';
        $value = number_format(intval($type . $this->value) / 100, 2, ',', '.');

        $tax = '';
        if (!empty($this->anticipation_id)) {
            $tax = number_format(intval($this->anticipation->tax) / 100, 2, ',', '.');
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
            'value_anticipable' => '0,00',
            'tax'               => $tax,
        ];
    }
}
