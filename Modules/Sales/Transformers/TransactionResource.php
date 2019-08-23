<?php

namespace Modules\Sales\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Lang;
use Vinkla\Hashids\Facades\Hashids;

class TransactionResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $sale = $this->getRelation('sale');

        if (!empty($sale->flag)) {
            $flag = $sale->flag;
        } else if ((!$this->flag || empty($this->flag)) && $this->payment_method == 1) {
            $flag = 'generico';
        } else {
            $flag = 'boleto';
        }
        return [
            'sale_code'        => '#' . Hashids::connection('sale_id')->encode($sale->id),
            'id'               => Hashids::connection('sale_id')->encode($sale->id),
            'project'          => $sale->projectModel->name,
            'product'          => (count($sale->getRelation('plansSales')) > 1) ? 'Carrinho' : $sale->getRelation('plansSales')
                                                                                                    ->first()
                                                                                                    ->getRelation('plan')->name,
            'client'           => $sale->clientModel()->first()->name,
            'method'           => $sale->payment_method,
            'status'           => $sale->status,
            'status_translate' => Lang::get('definitions.enum.sale.status.' . $sale->getEnum('status', $sale->status)),
            'start_date'       => $sale->start_date ? with(new Carbon($sale->start_date))->format('d/m/Y H:i:s') : '',
            'end_date'         => $sale->end_date ? with(new Carbon($sale->end_date))->format('d/m/Y H:i:s') : '',
            'total_paid'       => ($sale->dolar_quotation == '' ? 'R$ ' : 'US$ ') . substr_replace(@$this->value, ',', strlen(@$this->value) - 2, 0),
            'brand'            => $flag,
        ];
    }
}
