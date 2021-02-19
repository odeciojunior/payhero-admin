<?php

namespace Modules\Sales\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Affiliate;

/**
 * Class SalesResource
 * @package Modules\Sales\Transformers
 */
class SalesResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth()->user();
        $userPermissionRefunded = false;
        if ($user->hasRole('admin') || $user->hasRole('account_owner') || $user->hasPermissionTo('refund')) {
            $userPermissionRefunded = true;
        }

        $thankPageUrl = '';
        if (isset($this->project->domains[0]->name)) {
            $thankPageUrl =  'https://checkout.' . $this->project->domains[0]->name . '/order/' . Hashids::connection('sale_id')->encode($this->id);
        }

        $data = [
            //hide ids
            'id' => Hashids::connection('sale_id')->encode($this->id),
            'upsell' => Hashids::connection('sale_id')->encode($this->upsell_id),
            'delivery_id' => Hashids::encode($this->delivery_id),
            'checkout_id' => Hashids::encode($this->checkout_id),
            'client_id' => Hashids::encode($this->customer_id),
            //sale
            'payment_method' => $this->payment_method,
            'flag' => $this->flag,
            'start_date' => $this->start_date,
            'hours' => $this->hours,
            'status' => $this->status,
            'dolar_quotation' => $this->dolar_quotation,
            'installments_amount' => $this->installments_amount,
            'boleto_link' => $this->boleto_link,
            'boleto_digitable_line' => $this->boleto_digitable_line,
            'boleto_due_date' => $this->boleto_due_date,
            'attempts' => $this->attempts,
            'shipment_value' => $this->shipment_value,
            'cupom_code' => $this->cupom_code,
            //invoices
            'invoices' => $this->details->invoices ?? null,
            //transaction
            'transaction_rate' => $this->details->transaction_rate ?? null,
            'percentage_rate' => $this->details->percentage_rate ?? null,
            //extra info
            'total' => $this->details->total ?? null,
            'subTotal' => $this->details->subTotal ?? null,
            'discount' => $this->details->discount ?? null,
            'comission' => $this->details->comission ?? null,
            'convertax_value' => $this->details->convertax_value ?? null,
            'taxa' => $this->details->taxa ?? null,
            'taxaReal' => $this->details->taxaReal ?? null,
            'installment_tax_value' => $this->present()->getInstallmentValue,
            'release_date' => $this->details->release_date,
            'affiliate_comission' => $this->details->affiliate_comission,
            'shopify_order' => $this->shopify_order ?? null,
            'automatic_discount' => $this->details->automatic_discount ?? 0,
            'refund_value' => $this->details->refund_value ?? '0,00',
            'value_anticipable' => $this->details->value_anticipable ?? null,
            'total_paid_value' => $this->details->total_paid_value,
            'userPermissionRefunded' => $userPermissionRefunded,
            'refund_observation' => $this->details->refund_observation,
            'user_changed_observation'=>$this->details->user_changed_observation,
            'is_chargeback_recovered'    => $this->is_chargeback_recovered,
            'observation' => $this->observation,
            'thank_page_url'    => $thankPageUrl,
            'company_name' => $this->details->company_name,
            'has_order_bump' => $this->has_order_bump,
        ];
        $shopifyIntegrations = $this->project->shopifyIntegrations->where('status', 2);
        if (count($shopifyIntegrations) > 0) {
            $data['has_shopify_integration'] = true;
        } else {
            $data['has_shopify_integration'] = null;
        }

        if ($this->owner_id == auth()->user()->account_owner_id) {
            $data['user_sale_type'] = 'producer';
        } else {
            $data['user_sale_type'] = 'affiliate';
        }

        if (!empty($this->affiliate_id)) {
            $affiliate = Affiliate::withTrashed()->find($this->affiliate_id);
            $data['affiliate'] = $affiliate->user->name;
        } else {
            $data['affiliate'] = null;
        }

        return $data;
    }
}
