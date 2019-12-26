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

        $anticipableValue = '';
        if (!empty($this->anticipation_id)) {
            $anticipableValue = ' ( R$ ' . number_format(intval($this->antecipable_value) / 100, 2, ',', '.') . ' antecipado em ' . Carbon::createFromFormat('Y-m-d H:i:s', $this->anticipationCreatedAt)
                                                                                                                                          ->format('d/m/Y') . ')';
        }
        if (!empty($this->transaction) && empty($this->reason)) {
            $reason = 'Transação';
        } else if (!empty($this->transaction) && $this->reason == 'chargedback') {
            $reason = 'Chargeback';
        } else if (empty($this->transaction) && $this->reason == 'chargedback') {
            $reason = 'Chargeback';
        } else if (!empty($this->transaction) && $this->reason == 'refunded') {
            $reason = 'Estorno da transação';
        } else {
            $reason = $this->reason;
        }
        $value = number_format(intval($this->value) / 100, 2, ',', '.');

        $isOwner = $this->transaction_type == $transactionPresenter->getType('producer') || is_null($this->transaction_type);

        return [
            'id'                => Hashids::encode($this->id),
            'type'              => $this->type,
            'type_enum'         => $this->type_enum,
            'anticipable_value' => $anticipableValue,
            'value'             => $this->currency == 'dolar' ? '$ ' . $value : 'R$ ' . $value,
            'reason'            => $reason,
            'transaction_id'    => Hashids::connection('sale_id')->encode($this->sale_id),
            'sale_id'           => Hashids::connection('sale_id')->encode($this->sale_id),
            'date'              => $this->created_at->format('d/m/Y'),
            'is_owner'          => $isOwner,
        ];
    }
}
