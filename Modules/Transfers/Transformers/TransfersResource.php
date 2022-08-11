<?php

namespace Modules\Transfers\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Transaction;

class TransfersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $type = $this->type_enum == 2 ? "-" : "";

        return [
            "id" => hashids_encode($this->id),
            "sale_id" => hashids_encode($this->sale_id, "sale_id"),
            "type_enum" => $this->type_enum,
            "value" => number_format(intval($type . $this->value) / 100, 2, ",", "."),
            "reason" => $this->getReason(),
            "anticipation_id" => $this->getAnticipatedCode(),
            "is_owner" => $this->transaction_type == Transaction::TYPE_PRODUCER || is_null($this->transaction_type),
            "date_request" => !empty($this->transaction)
                ? Carbon::parse($this->transaction->sale->start_date)->format("d/m/Y")
                : "",
            "date_request_time" => !empty($this->transaction)
                ? Carbon::parse($this->transaction->sale->start_date)->format("H:i")
                : "",
            "date_release" => $this->created_at->format("d/m/Y"),
            "date_release_time" => $this->created_at->format("H:i"),
            "tax" => !empty($this->anticipation_id)
                ? number_format(intval($this->anticipation->tax) / 100, 2, ",", ".")
                : "",
        ];
    }

    public function getReason()
    {
        if (!empty($this->transaction) && empty($this->reason)) {
            return "Transação";
        } elseif (!empty($this->transaction) && $this->reason == "chargedback") {
            return "Chargeback";
        } elseif ($this->reason == "refunded") {
            return "Estorno da transação";
        } elseif ($this->reason == "canceled_antifraud") {
            return "Chargeback";
        }

        return $this->reason;
    }

    public function getAnticipatedCode()
    {
        $codeAnticipation = null;

        if (!empty($this->anticipation_id)) {
            $anticipation = $this->anticipation->first();
            $codeAnticipation = hashids_encode($anticipation->id, "anticipation_id");
        } elseif (!empty($this->transaction_id) && !empty($this->transaction->anticipatedTransactions()->first())) {
            $anticipatedTransaction = $this->transaction->anticipatedTransactions()->first();
            $codeAnticipation = hashids_encode($anticipatedTransaction->anticipation_id, "anticipation_id");
        }
        return $codeAnticipation;
    }
}
