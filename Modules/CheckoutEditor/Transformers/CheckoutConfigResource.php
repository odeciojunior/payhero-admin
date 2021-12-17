<?php

namespace Modules\CheckoutEditor\Transformers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutConfigResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => hashids_encode($this->id),
            'project_id' => hashids_encode($this->project_id),
            'checkout_type_enum' => $this->checkout_type_enum,
            'checkout_logo_enabled' => $this->checkout_logo_enabled,
            'checkout_logo' => $this->checkout_logo,
            'checkout_banner_enabled' => $this->checkout_banner_enabled,
            'checkout_banner_type' => $this->checkout_banner_type,
            'checkout_banner' => $this->checkout_banner,
            'countdown_enabled' => $this->countdown_enabled,
            'countdown_time' => $this->countdown_time,
            'countdown_description' => $this->countdown_description,
            'countdown_finish_message' => $this->countdown_finish_message,
            'topbar_enabled' => $this->topbar_enabled,
            'topbar_content' => $this->topbar_content,
            'notifications_enabled' => $this->notifications_enabled,
            'notifications_interval' => $this->notifications_interval,
            'notification_buying_enabled' => $this->notification_buying_enabled,
            'notification_buying_minimum' => $this->notification_buying_minimum,
            'notification_bought_30_minutes_enabled' => $this->notification_bought_30_minutes_enabled,
            'notification_bought_30_minutes_minimum' => $this->notification_bought_30_minutes_minimum,
            'notification_bought_last_hour_enabled' => $this->notification_bought_last_hour_enabled,
            'notification_bought_last_hour_minimum' => $this->notification_bought_last_hour_minimum,
            'notification_just_bought_enabled' => $this->notification_just_bought_enabled,
            'notification_just_bought_minimum' => $this->notification_just_bought_minimum,
            'social_proof_enabled' => $this->social_proof_enabled,
            'social_proof_message' => $this->social_proof_message,
            'social_proof_minimum' => $this->social_proof_minimum,
            'invoice_description' => $this->invoice_description,
            'company_id' => $this->company_id,
            'cpf_enabled' => $this->cpf_enabled,
            'cnpj_enabled' => $this->cnpj_enabled,
            'credit_card_enabled' => $this->credit_card_enabled,
            'bank_slip_enabled' => $this->bank_slip_enabled,
            'pix_enabled' => $this->pix_enabled,
            'quantity_selector_enabled' => $this->quantity_selector_enabled,
            'email_required' => $this->email_required,
            'installments_limit' => $this->installments_limit,
            'interest_free_installments' => $this->interest_free_installments,
            'preselected_installment' => $this->preselected_installment,
            'bank_slip_due_days' => $this->bank_slip_due_days,
            'automatic_discount_credit_card' => $this->automatic_discount_credit_card,
            'automatic_discount_bank_slip' => $this->automatic_discount_bank_slip,
            'automatic_discount_pix' => $this->automatic_discount_pix,
            'post_purchase_message_enabled' => $this->post_purchase_message_enabled,
            'post_purchase_message_title' => $this->post_purchase_message_title,
            'post_purchase_message_content' => $this->post_purchase_message_content,
            'whatsapp_enabled' => $this->whatsapp_enabled,
            'support_phone' => $this->support_phone,
            'support_phone_verified' => $this->support_phone_verified,
            'support_email' => $this->support_email,
            'support_email_verified' => $this->support_email_verified,
            'color_primary' => $this->color_primary,
            'color_secondary' => $this->color_secondary,
            'color_buy_button' => $this->color_buy_button,
            'theme_enum' => $this->theme_enum,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at)->toDateTimeString(),
            'deleted_at' => Carbon::parse($this->deleted_at)->toDateTimeString(),
        ];
    }
}
