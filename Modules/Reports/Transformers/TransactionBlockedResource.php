<?php

namespace Modules\Reports\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Vinkla\Hashids\Facades\Hashids;

class TransactionBlockedResource extends JsonResource
{
    public function toArray($request)
    {
        $sale = $this->sale;

        if (!empty($sale->flag)) {
            $flag = $sale->flag;
        } else if ($sale->payment_method == 1 && empty($sale->flag)) {
            $flag = 'generico';
        } else if ($sale->payment_method == 3 && empty($sale->flag)) {
            $flag = 'debito';
        } else {
            $flag = 'boleto';
        }

        $data                = [
            'sale_code'        => '#' . Hashids::connection('sale_id')->encode($sale->id),
            'id'               => Hashids::connection('sale_id')->encode($sale->id),
            'upsell'           => Hashids::connection('sale_id')->encode($this->sale->upsell_id),
            'project'          => $sale->project->name,
            'product'          => (count($sale->getRelation('plansSales')) > 1) ? 'Carrinho' : $sale->plansSales->first()->plan->name,
            'client'           => $sale->customer->name,
            'method'           => $sale->payment_method,
            'status'           => $sale->status,
            'status_translate' => Lang::get('definitions.enum.sale.status.' . $sale->present()->getStatus($sale->status)),
            'start_date'       => $sale->start_date ? Carbon::parse($sale->start_date)->format('d/m/Y H:i:s') : '',
            'end_date'         => $sale->end_date ? Carbon::parse($sale->end_date)->format('d/m/Y H:i:s') : '',
            'total_paid'       => 'R$ ' . substr_replace(@$this->value, ',', strlen(@$this->value) - 2, 0),
            'brand'            => $flag,
        ];

        if($sale->status == 24) {
            $data['reason_blocked'] = 'Em disputa';
        } elseif(!isset($sale->tracking[0]->id)) {
            $data['reason_blocked'] = 'Sem rastreio';
        } else {
            $tracking = $sale->tracking->whereIn('system_status_enum', [2,3,4,5])->first();
            if($tracking->system_status_enum == 2) {
                $data['reason_blocked'] = 'Rastreio sem movimentação';
            } elseif($tracking->system_status_enum == 3) {
                $data['reason_blocked'] = 'Rastreio desconhecido';
            } elseif($tracking->system_status_enum == 4) {
                $data['reason_blocked'] = 'Rastreio postado antes da venda';
            } elseif($tracking->system_status_enum == 5) {
                $data['reason_blocked'] = 'Rastreio duplicado';
            }
        }

        if ($sale->owner_id == auth()->user()->account_owner_id) {
            $data['user_sale_type'] = 'producer';
        } else {
            $data['user_sale_type'] = 'affiliate';
        }

        if (!empty($sale->affiliate->id)) {
            $data['affiliate'] = $sale->affiliate->user->name;
        } else {
            $data['affiliate'] = null;
        }

        return $data;
    }
}


