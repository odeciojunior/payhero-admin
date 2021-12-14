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
            'checkout_type_enum',
            'checkout_logo',
            'checkout_banner_enabled',
            'checkout_banner_type',
            'checkout_banner',
            'countdown_enabled',
            'countdown_time',
            'countdown_description',
            'countdown_finish_message',
            'topbar_enabled',
            'topbar_content',
            'notifications_enabled',
            'notifications_interval',
            'notification_buying_enabled',
            'notification_buying_minimum',
            'notification_bought_30_minutes_enabled',
            'notification_bought_30_minutes_minimum',
            'notification_bought_last_hour_enabled',
            'notification_bought_last_hour_minimum',
            'notification_just_bought_enabled',
            'notification_just_bought_minimum',
            'social_proof_enabled',
            'social_proof_message',
            'social_proof_minimum',
            'invoice_description',
            'company_id',
            'cpf_enabled',
            'cnpj_enabled',
            'credit_card_enabled',
            'bank_slip_enabled',
            'pix_enabled',
            'quantity_selector_enabled',
            'email_required',
            'installments_limit',
            'interest_free_installments',
            'preselected_installment',
            'bank_slip_due_days',
            'automatic_discount_credit_card',
            'automatic_discount_bank_slip',
            'automatic_discount_pix',
            'post_purchase_message_enabled',
            'post_purchase_message_title',
            'post_purchase_message_content',
            'whatsapp_enabled',
            'support_phone',
            'color_primary',
            'color_secondary',
            'color_buy_button',
            'theme_enum',
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at)->toDateTimeString(),
            'deleted_at' => Carbon::parse($this->deleted_at)->toDateTimeString(),
        ];
    }
}
