<?php

namespace Modules\Reports\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;

class PendingBalanceResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
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

        $data = [
            'id'         => Hashids::connection('sale_id')->encode($sale->id),
            'sale_code'  => '#' . Hashids::connection('sale_id')->encode($sale->id),
            'brand'      => $flag ?? '',
            'project'    => $sale->project->name ?? '',
            'client'     => $sale->customer->name ?? '',
            'start_date' => $sale->start_date ? Carbon::parse($sale->start_date)->format('d/m/Y H:i:s') : '',
            'end_date'   => $sale->end_date ? Carbon::parse($sale->end_date)->format('d/m/Y H:i:s') : '',
            'total_paid' => 'R$ ' . substr_replace(@$this->value, ',', strlen(@$this->value) - 2, 0),
        ];

        return $data;
    }
}
