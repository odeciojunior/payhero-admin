<?php

namespace Modules\Sales\Transformers;

use Carbon\Carbon;
use App\Entities\Plan;
use App\Entities\Client;
use App\Entities\Company;
use App\Entities\Project;
use App\Entities\PlanSale;
use App\Entities\Transaction;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class SalesResource extends Resource
{
    public function toArray($request)
    {
        if ($this->flag) {
            $this->flag = $this->flag;
        } else if ((!$this->flag || empty($this->flag)) && $this->payment_method == 1) {
            $this->flag = 'generico';
        } else {
            $this->flag = 'boleto';
        }
 
        return [
            'sale_code'  => '#' . Hashids::connection('sale_id')->encode($this->id),
            'id'         => Hashids::connection('sale_id')->encode($this->id),
            'project'    => $this->getRelation('plansSales')->first()->getRelation('plan')
                                 ->getRelation('projectId')->name,
            'product'    => (count($this->getRelation('plansSales')) > 1) ? 'Carrinho' : $this->getRelation('plansSales')
                                                                                              ->first()
                                                                                              ->getRelation('plan')->name,
            'client'     => $this->getRelation('clientModel')->name,
            'method'     => $this->payment_method,
            'status'     => $this->status,
            'start_date' => $this->start_date ? with(new Carbon($this->start_date))->format('d/m/Y H:i:s') : '',
            'end_date'   => $this->end_date ? with(new Carbon($this->end_date))->format('d/m/Y H:i:s') : '',
            'total_paid' => ($this->dolar_quotation == '' ? 'R$ ' : 'US$ ') . substr_replace(@$this->getRelation('transactions')[0]->value, ',', strlen(@$this->getRelation('transactions')[0]->value) - 2, 0),
            'brand'      => $this->flag,
        ];
    }
}
