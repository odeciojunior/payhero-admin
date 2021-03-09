<?php

namespace Modules\Sales\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\Affiliate;
use Vinkla\Hashids\Facades\Hashids;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        $sale = $this->sale;

        if (!empty($sale->flag)) {
            $flag = $sale->flag;
        } elseif ($sale->payment_method == 1 && empty($sale->flag)) {
            $flag = 'generico';
        } elseif ($sale->payment_method == 3 && empty($sale->flag)) {
            $flag = 'debito';
        } else {
            $flag = 'boleto';
        }

        $data                = [
            'sale_code'        => '#' . Hashids::connection('sale_id')->encode($sale->id),
            'id'               => Hashids::connection('sale_id')->encode($sale->id),
            'id_default'       => Hashids::encode($this->sale->id),
            'upsell'           => Hashids::connection('sale_id')->encode($this->sale->upsell_id),
            'project'          => $sale->project->name,
            'product'          => (count($sale->getRelation('plansSales')) > 1) ? 'Carrinho' : $sale->plansSales->first()->plan->name,
            'client'           => $sale->customer->name,
            'method'           => $sale->payment_method,
            'status'           => $sale->status,
            'status_translate' => Lang::get('definitions.enum.sale.status.' . $sale->present()
                                                                                   ->getStatus($sale->status)),
            'start_date'       => $sale->start_date ? Carbon::parse($sale->start_date)->format('d/m/Y H:i:s') : '',
            'end_date'         => $sale->end_date ? Carbon::parse($sale->end_date)->format('d/m/Y H:i:s') : '',
            'total_paid'       => 'R$ ' . substr_replace(@$this->value, ',', strlen(@$this->value) - 2, 0),
            'brand'            => $flag,
            'email_status'     => $sale->checkout ? $sale->checkout->present()->getEmailSentAmount() : 'Não enviado',
            'sms_status'       => $sale->checkout ? $sale->checkout->present()->getSmsSentAmount() : 'Não enviado',
            'recovery_status'  => $sale->checkout ? ($sale->checkout->status == 'abandoned cart' ? 'Não recuperado' : 'Recuperado') : '',
            'whatsapp_link'    => "https://api.whatsapp.com/send?phone=" . preg_replace('/\D/', '', $sale->customer->telephone) . '&text=Olá ' . explode(' ', preg_replace('/\D/', '', $sale->customer->name))[0],
            'total',
            'shopify_order'    => $sale->shopify_order ?? null,
            'is_chargeback_recovered'    => $sale->is_chargeback_recovered,
            'observation'      => $sale->observation,
            'cupom_code'       => $sale->cupom_code ?? null,
            'has_order_bump'   => $sale->has_order_bump,
            'has_contestation' => $sale->contestations->count() ? true : false,
        ];
        $shopifyIntegrations = $sale->project->shopifyIntegrations->where('status', 2);

        if (count($shopifyIntegrations) > 0) {
            $data['has_shopify_integration'] = true;
        } else {
            $data['has_shopify_integration'] = null;
        }

        $data['cashback_value'] = '0.00';
        if ($sale->owner_id == auth()->user()->account_owner_id) {
            $data['user_sale_type'] = 'producer';
            if(!empty($sale->cashback->value)) {
                $data['cashback_value'] = 'R$ ' . substr_replace(@$sale->cashback->value, ',', strlen(@$sale->cashback->value) - 2, 0);
            }
        } else {
            $data['user_sale_type'] = 'affiliate';
        }

        if (!empty($sale->affiliate_id)) {
            $affiliate         = Affiliate::withTrashed()->find($sale->affiliate_id);
            $data['affiliate'] = $affiliate->user->name;
        } else {
            $data['affiliate'] = null;
        }

        if ($sale->start_date <= Carbon::now()->subMinutes(5)->toDateTimeString()) {
            $data['date_before_five_minutes_ago'] = true;
        } else {
            $data['date_before_five_minutes_ago'] = false;
        }

        return $data;
    }
}


