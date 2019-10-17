<?php

namespace Modules\Sales\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;

class TransactionResource extends Resource
{
    public function toArray($request)
    {
        $sale = $this->sale;

        if (!empty($sale->flag)) {
            $flag = $sale->flag;
        } else if ($sale->payment_method == 1 && empty($sale->flag)) {
            $flag = 'generico';
        } else {
            $flag = 'boleto';
        }

        return [
            'sale_code'        => '#' . Hashids::connection('sale_id')->encode($sale->id),
            'id'               => Hashids::connection('sale_id')->encode($sale->id),
            'id_default'       => Hashids::encode($this->sale->id),
            'project'          => $sale->project->name,
            'product'          => (count($sale->getRelation('plansSales')) > 1) ? 'Carrinho' : $sale->plansSales->first()->plan->name,
            'client'           => $sale->client->name,
            'method'           => $sale->payment_method,
            'status'           => $sale->status,
            'status_translate' => Lang::get('definitions.enum.sale.status.' . $sale->present()
                                                                                   ->getStatus($sale->status)),
            'start_date'       => $sale->start_date ? with(new Carbon($sale->start_date))->format('d/m/Y H:i:s') : '',
            'end_date'         => $sale->end_date ? with(new Carbon($sale->end_date))->format('d/m/Y H:i:s') : '',
            'total_paid'       => ($sale->dolar_quotation == '' ? 'R$ ' : 'US$ ') . substr_replace(@$this->value, ',', strlen(@$this->value) - 2, 0),
            'brand'            => $flag,
            'email_status'     => $sale->checkout ? $sale->checkout->present()->getEmailSentAmount() : 'Não enviado',
            'sms_status'       => $sale->checkout ? $sale->checkout->present()->getSmsSentAmount() : 'Não enviado',
            'recovery_status'  => $sale->checkout ? ($sale->checkout->status == 'abandoned cart' ? 'Não recuperado' : 'Recuperado') : '',
            'whatsapp_link'    => "https://api.whatsapp.com/send?phone=" . FoxUtils::prepareCellPhoneNumber(preg_replace('/\D/', '', $sale->client->telephone)) . '&text=Olá ' . explode(' ', preg_replace('/\D/', '', $sale->client->name))[0],
            'total',
        ];
    }
}
