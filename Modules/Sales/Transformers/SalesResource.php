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

class SalesResource extends Resource {

    public function toArray($request) {

        $client = Client::find($this->client);

        $plansSale = PlanSale::where('sale',$this->id)->get()->toArray();
        $product = '';
        $project = '';
        foreach($plansSale as $planSale){
            $plano = Plan::find($planSale['plan']);
            $product = $plano['name'];
            $project = Project::find($plano['project']);
            $project = $project['name'];
        }

        if(count($plansSale) > 1){
            $product = "Carrinho";
        }

        $userCompanies = Company::where('user', \Auth::user()->id)->pluck('id');

        $transaction = Transaction::where('sale',$this->id)->whereIn('company',$userCompanies)->first();

        if($transaction){
            $value = $transaction->value;
        }
        else{
            $value = '000';
        }

        return [
            'id'              => '#'.$this->id,
            'projeto'         => $project,
            'produto'         => $product,
            'comprador'       => $client['name'],
            'forma'           => $this->payment_form == 'cartao_credito' ? 'cartão' : $this->payment_form,
            'status'          => $this->gateway_status,
            'data_inicio'     => $this->start_date ? with(new Carbon($this->start_date))->format('d/m/Y H:i:s') : '',
            'data_finalizada' => $this->end_date ? with(new Carbon($this->end_date))->format('d/m/Y H:i:s') : '',
            'total_pago'      => ($this->dolar_quotation == '' ? 'R$ ' : 'US$ ') . substr_replace($value, '.', strlen($value) - 2, 0 ),
        ];

    }
}
