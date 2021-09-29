<?php

namespace Modules\Projects\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Affiliate;

/**
 * @property mixed id
 * @property mixed photo
 * @property mixed name
 * @property mixed description
 * @property mixed created_at
 * @property mixed shopify_id
 * @property mixed logo
 * @property mixed url_page
 * @property mixed contact
 * @property mixed support_phone
 * @property mixed invoice_description
 * @property mixed installments_amount
 * @property mixed installments_interest_free
 * @property mixed boleto_redirect
 * @property mixed card_redirect
 * @property mixed analyzing_redirect
 * @property mixed boleto
 * Class ProjectsResource
 * @package Modules\Projects\Transformers
 */
class ProjectsResource extends JsonResource
{
    public function toArray($request): array
    {
        if (isset($this->affiliate_id)) {
            $affiliate = '';
            if ($this->affiliate_id > 0) {
                $affiliate = new Affiliate();
                $affiliate->id = $this->affiliate_id;
                $affiliate->created_at = $this->affiliate_created_at;
                $affiliate->percentage = $this->affiliate_percentage;
                $affiliate->status_enum = $this->affiliate_status;
            }
        } else {
            $affiliate = $this->affiliates[0] ?? '';
        }
        $affiliated = !empty($affiliate);

        $notazzConfig = json_decode($this->notazz_configs);

        return [
            'id' => hashids_encode($this->id),
            'photo' => $this->photo,
            'name' => $this->name,
            'description' => $this->description,
            'discount_recovery_status' => $this->discount_recovery_status,
            'discount_recovery_value' => $this->discount_recovery_value,
            'created_at' => (new Carbon($this->created_at))->format('d/m/Y'),
            'shopify_id' => $this->shopify_id,
            'woocommerce_id' => $this->woocommerce_id,
            'logo' => $this->logo,
            'url_page' => $this->url_page,
            'contact' => $this->contact ?? '',
            'contact_verified' => $this->contact_verified,
            'support_phone' => $this->support_phone ?? '',
            'support_phone_verified' => $this->support_phone_verified,
            'invoice_description' => $this->invoice_description,
            'installments_amount' => $this->installments_amount,
            'installments_interest_free' => $this->installments_interest_free,
            'boleto' => $this->boleto,
            'credit_card' => $this->credit_card,
            'pix' => $this->pix,
            'boleto_due_days' => $this->boleto_due_days,
            'boleto_redirect' => $this->boleto_redirect,
            'billet_release_days' => $this->usersProjects->first()->company->gateway_release_money_days ?? '',
            'card_redirect' => $this->card_redirect,
            'pix_redirect' => $this->pix_redirect,
            'analyzing_redirect' => $this->analyzing_redirect,
            'cost_currency_type' => $this->present()->getCurrencyCost($notazzConfig->cost_currency_type ?? 1),
            'update_cost_shopify' => $notazzConfig->update_cost_shopify ?? 1,
            'status' => isset($this->domains[0]->name) ? 1 : 0,
            'checkout_type' => $this->checkout_type,
            "terms_affiliates" => $this->terms_affiliates,
            "cookie_duration" => $this->cookie_duration,
            "automatic_affiliation" => $this->automatic_affiliation,
            "url_affiliates" => route('affiliates.index', hashids_encode($this->id)),
            "percentage_affiliates" => $this->percentage_affiliates,
            'affiliated' => $affiliated,
            'affiliate_id' => hashids_encode($affiliate->id ?? ''),
            'affiliate_date' => (!empty($affiliate->created_at)) ? (new Carbon($affiliate->created_at))->format(
                'd/m/Y'
            ) : '',
            "status_url_affiliates" => $this->status_url_affiliates,
            "commission_type_enum" => $this->commission_type_enum,
            "commission_affiliate" => $affiliate->percentage ?? '',
            "status_affiliate" => $affiliate->status_enum ?? '',
            "producer" => $this->producer ?? '',
            'whatsapp_button' => $this->whatsapp_button,
            'credit_card_discount' => $this->credit_card_discount,
            'billet_discount' => $this->billet_discount,
            'pix_discount' => $this->pix_discount,
            'pre_selected_installment' => $this->pre_selected_installment,
            'required_email_checkout' => $this->required_email_checkout,
            'document_type_checkout' => $this->document_type_checkout,
            'countdown_timer_flag' => $this->countdown_timer_flag,
            'countdown_timer_color' => $this->countdown_timer_color,
            'countdown_timer_time' => $this->countdown_timer_time,
            'countdown_timer_description' => $this->countdown_timer_description,
            'countdown_timer_finished_message' => $this->countdown_timer_finished_message,
            'reviews_config_icon_type' => $this->reviews_config_icon_type,
            'reviews_config_icon_color' => $this->reviews_config_icon_color,
            'custom_message_configs'=>$this->custom_message_configs,
            'product_amount_selector' => $this->product_amount_selector,
            'finalizing_purchase_config_toogle' => $this->finalizing_purchase_config_toogle,
            'finalizing_purchase_config_text' => $this->finalizing_purchase_config_text,
            'finalizing_purchase_config_min_value' => $this->finalizing_purchase_config_min_value,
            'checkout_notification_config_toogle' => $this->checkout_notification_configs_toogle,
            'checkout_notification_config_time' => $this->checkout_notification_configs_time,
            'checkout_notification_config_mobile' => $this->checkout_notification_configs_mobile,
            'checkout_notification_config_messages' => $this->checkout_notification_configs_message,
            'checkout_notification_config_messages_min_value' => $this->checkout_notification_configs_message_min_value,
            'chargeback_count' => $this->chargeback_count ?? 0,
            'open_tickets' => $this->open_tickets ?? 0,
            'without_tracking' => $this->without_tracking ?? 0,
            'approved_sales' => $this->approved_sales ?? 0,
            'approved_sales_value' => $this->approved_sales_value ? substr_replace(
                @$this->approved_sales_value,
                '.',
                strlen(
                    @$this->approved_sales_value
                ) - 2,
                0
            ) : 0,
        ];
    }
}
