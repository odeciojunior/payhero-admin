<?php

namespace Modules\Sales\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Core\Entities\Affiliate;

/**
 * Class SalesResource
 * @package Modules\Sales\Transformers
 */
class SalesResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data                = [
            //hide ids
            'id'                    => Hashids::connection('sale_id')->encode($this->id),
            'delivery_id'           => Hashids::encode($this->delivery_id),
            'checkout_id'           => Hashids::encode($this->checkout_id),
            'client_id'             => Hashids::encode($this->customer_id),
            //sale
            'payment_method'        => $this->payment_method,
            'flag'                  => $this->flag,
            'start_date'            => $this->start_date,
            'hours'                 => $this->hours,
            'status'                => $this->status,
            'dolar_quotation'       => $this->dolar_quotation,
            'iof'                   => $this->iof,
            'installments_amount'   => $this->installments_amount,
            'boleto_link'           => $this->boleto_link,
            'boleto_digitable_line' => $this->boleto_digitable_line,
            'boleto_due_date'       => $this->boleto_due_date,
            'attempts'              => $this->attempts,
            'shipment_value'        => $this->shipment_value,
            //invoices
            'invoices'              => $this->details->invoices ?? null,
            //transaction
            'transaction_rate'      => $this->details->transaction_rate ?? null,
            'percentage_rate'       => $this->details->percentage_rate ?? null,
            //extra info
            'total'                 => $this->details->total ?? null,
            'subTotal'              => $this->details->subTotal ?? null,
            'discount'              => $this->details->discount ?? null,
            'comission'             => $this->details->comission ?? null,
            'convertax_value'       => $this->details->convertax_value ?? null,
            'taxa'                  => $this->details->taxa ?? null,
            'taxaReal'              => $this->details->taxaReal ?? null,
            'installment_tax_value' => $this->present()->getInstallmentValue,
            'release_date'          => $this->details->release_date,
            'affiliate_comission'   => $this->details->affiliate_comission,
            'shopify_order'         => $this->shopify_order ?? null,
            'automatic_discount'    => $this->details->automatic_discount ?? 0,
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
