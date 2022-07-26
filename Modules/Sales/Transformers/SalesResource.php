<?php

namespace Modules\Sales\Transformers;

use Exception;
use Google\Service\ShoppingContent\Amount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleWoocommerceRequests;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\WooCommerceService;
use Vinkla\Hashids\Facades\Hashids;

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
        if ($user->hasAnyPermission(['sales_manage','finances_manage'])) {
            $userPermissionRefunded = true;
        }

        $thankPageUrl = '';
        $thankLabelText = 'Link página de obrigado:';

        $domain = Domain::select('name')->where('project_id', $this->project_id)->where('status', 3)->first();
        $domainName = $domain->name??'cloudfox.net';

        if (!empty($domain->name)) {
            $urlCheckout = "https://checkout.{$domain->name}/order/";
            if (config('app.env') == 'homolog') {
                $urlCheckout = "https://checkout-test.cloudfox.net/order/";
            }
            $thankPageUrl = $urlCheckout . Hashids::connection('sale_id')->encode($this->id);
        }

        if ($this->payment_method == 4 && $this->status <> Sale::STATUS_APPROVED) {
            $thankLabelText = 'Link página de Qrcode:';
        }
        // if($this->progressive_discount){
        //     $total = (FoxUtils::formatMoney( (FoxUtils::onlyNumbers($this->details->total) - $this->progressive_discount) / 100) );
        //     $this->details->total = $total;
        // }

        
        $boletoLink = "https://checkout.{$domainName}/order/".Hashids::connection('sale_id')->encode($this->id)."/download-boleto";

        $data = [
            'id' => hashids_encode($this->id, 'sale_id'),
            'upsell' => hashids_encode($this->upsell_id, 'sale_id'),
            'delivery_id' => hashids_encode($this->delivery_id),
            'checkout_id' => hashids_encode($this->checkout_id),
            'client_id' => hashids_encode($this->customer_id),
            //sale
            'payment_method' => $this->payment_method,
            'flag' => !empty($this->flag) ? $this->flag : $this->present()->getPaymentFlag(),
            'start_date' => $this->start_date,
            'hours' => $this->hours,
            'status' => $this->status,
            'status_name' => $this->present()->getStatus($this->status),
            'dolar_quotation' => $this->dolar_quotation,
            'installments_amount' => $this->installments_amount,
            'boleto_link' => $boletoLink,
            'boleto_digitable_line' => $this->boleto_digitable_line,
            'boleto_due_date' => $this->boleto_due_date,
            'attempts' => $this->attempts,
            'shipment_value' => FoxUtils::formatMoney(FoxUtils::onlyNumbers($this->shipment_value) / 100),
            'cupom_code' => $this->cupom_code,
            //invoices
            'invoices' => $this->details->invoices ?? null,
            //transaction
            'transaction_rate' => $this->details->transaction_rate ?? null,
            'tax' => $this->details->tax ?? null,
            'tax_type' => $this->details->tax_type ?? null,
            'checkout_tax' => $this->details->checkout_tax ?? null,
            //extra info
            'total' => $this->details->total ?? null,
            'subTotal' => $this->details->subTotal ?? null,
            'progressive_discount' => FoxUtils::formatMoney($this->progressive_discount / 100,) ?? null,
            'discount' => $this->details->discount ?? null,
            'comission' => $this->details->comission ?? 0 + ($this->cashback->value ?? 0),
            'convertax_value' => $this->details->convertax_value ?? null,
            'taxa' => $this->details->taxa ?? null,
            'taxaReal' => $this->details->taxaReal ?? null,
            'taxaDiscount' => $this->details->taxaDiscount ?? null,
            'totalTax' => $this->details->totalTax ?? null,
            'installment_tax_value' => FoxUtils::formatMoney($this->installment_tax_value / 100,),
            'release_date' => $this->details->release_date,
            'affiliate_comission' => $this->details->affiliate_comission,
            'shopify_order' => $this->shopify_order ?? null,
            'woocommerce_order' => $this->woocommerce_order ?? null,
            'automatic_discount' => $this->details->automatic_discount ?? 0,
            'refund_value' => $this->details->refund_value ?? '0,00',
            'value_anticipable' => $this->details->value_anticipable ?? null,
            'total_paid_value' => $this->details->total_paid_value,
            'userPermissionRefunded' => $userPermissionRefunded,
            'refund_observation' => $this->details->refund_observation,
            'user_changed_observation' => $this->details->user_changed_observation,
            'is_chargeback_recovered' => $this->is_chargeback_recovered,
            'observation' => $this->observation,
            'thank_page_url' => $thankPageUrl,
            'thank_label_text' => $thankLabelText,
            'company_name' => $this->details->company_name,
            'has_order_bump' => $this->has_order_bump,
            'has_contestation' => $this->contestations->count() ? true : false,
            'cashback_value' => $this->payment_method <> 4 ? (isset($this->cashback->value) ? FoxUtils::formatMoney($this->cashback->value / 100) : 0):0 ,
            'has_cashback' => $this->cashback->value ?? false,
            'api_flag' => $this->api_flag,
            'has_withdrawal' => !empty($this->details->has_withdrawal),
            'gateway_id' => hashids_encode($this->gateway_id)
        ];

        $shopifyIntegrations = [];
        if(!empty($this->project)){
            $shopifyIntegrations = $this->project->shopifyIntegrations->where('status', 2);
        }

        $data['asaas_amount_refund'] = '';
        if(in_array($this->gateway_id,[Gateway::ASAAS_PRODUCTION_ID,Gateway::ASAAS_SANDBOX_ID]))
        {
            $data['asaas_amount_refund'] = $this->getSalesTaxesChargeback();
        }

        $data['has_shopify_integration'] = null;
        if (count($shopifyIntegrations) > 0)
        {
            $data['has_shopify_integration'] = true;
        }

        $woocommerceIntegrations = [];
        if(!empty($this->project)){
            $woocommerceIntegrations = $this->project->woocommerceIntegrations->where('status', 2);
        }
        $data['has_woocommerce_integration'] = null;
        if (count($woocommerceIntegrations) > 0)
        {
            $data['has_woocommerce_integration'] = true;

            if(!empty($this->woocommerce_order)){
                try{

                    $integration = WooCommerceIntegration::where('project_id', $this->project_id)->first();
                    $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);
                    $order = $service->woocommerce->get('orders/'.$this->woocommerce_order);
                    $data['woocommerce_order'] = $order;
                }catch(Exception $e){
                    $data['woocommerce_order'] = ['status'=>'Pedido não encontrado'];

                }

            }else{
                $request = SaleWoocommerceRequests::where('sale_id', $this->id)
                ->where('method', 'CreatePendingOrder')
                ->where('status', 0)
                ->first();

                if(!empty($request) && $this->status == 1){
                    $data['woocommerce_retry_order'] = true;
                }
            }
        }

        $data['user_sale_type'] = 'producer';
        if ($this->owner_id <> auth()->user()->account_owner_id) {
            $data['user_sale_type'] = 'affiliate';
        }

        $data['affiliate'] = null;
        if (!empty($this->affiliate_id)) {
            $affiliate = Affiliate::withTrashed()->find($this->affiliate_id);
            $data['affiliate'] = $affiliate->user->name;
        }

        $data['total_parcial'] = foxutils()->onlyNumbers($data['total']) + foxutils()->onlyNumbers($data['cashback_value']);
        $data['total_parcial'] = FoxUtils::formatMoney($data['total_parcial'] / 100);

        return $data;
    }

    public function getSalesTaxesChargeback()
    {
        $cashbackValue = !empty($this->cashback) ? $this->cashback->value:0;
        $saleTax = (new SaleService)->getSaleTaxRefund($this,$cashbackValue);

        return FoxUtils::formatMoney(($saleTax + foxutils()->onlyNumbers($this->details->comission)) / 100);
    }
}
