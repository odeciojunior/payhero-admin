<?php

namespace Modules\CheckoutEditor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCheckoutConfigRequest extends FormRequest
{
    public function rules()
    {
        return [
            'checkout_type_enum' => 'required|integer',
            'checkout_logo_enabled' => 'required|boolean',
            'checkout_logo' => 'required_if:checkout_logo_enabled,1file',
            'checkout_banner_enabled' => 'required|boolean',
            'checkout_banner_type' => 'required_if:checkout_banner_enabled,1|integer',
            'checkout_banner' => 'file',
            'checkout_favicon_enabled' => 'required|boolean',
            'checkout_favicon_type' => 'required_if:checkout_favicon_enabled,1|integer',
            'checkout_favicon' => 'file',
            'countdown_enabled' => 'required|boolean',
            'countdown_time' => 'required_if:countdown_enabled,1|integer|nullable',
            'countdown_description' => 'nullable|string',
            'countdown_finish_message' => 'required_if:countdown_enabled,1|string|nullable',
            'topbar_enabled' => 'required|boolean',
            'topbar_content' => 'required_if:topbar_enabled,1|string|nullable',
            'notifications_enabled' => 'required|boolean',
            'notifications_interval' => 'required_if:notifications_enabled,1|integer',
            'notification_buying_enabled' => 'required_if:notifications_enabled,1|boolean',
            'notification_buying_minimum' => 'required_if:notifications_enabled,1|integer|nullable',
            'notification_bought_30_minutes_enabled' => 'required_if:notifications_enabled,1|boolean',
            'notification_bought_30_minutes_minimum' => 'required_if:notifications_enabled,1|integer|nullable',
            'notification_bought_last_hour_enabled' => 'required_if:notifications_enabled,1|boolean',
            'notification_bought_last_hour_minimum' => 'required_if:notifications_enabled,1|integer|nullable',
            'notification_just_bought_enabled' => 'required_if:notifications_enabled,1|boolean',
            'notification_just_bought_minimum' => 'required_if:notifications_enabled,1|integer|nullable',
            'social_proof_enabled' => 'required|boolean',
            'social_proof_message' => 'required_if:social_proof_enabled,1|string|nullable',
            'social_proof_minimum' => 'required_if:social_proof_enabled,1|integer|nullable',
            'invoice_description' => 'nullable|string',
            'company_id' => 'required|string',
            'cpf_enabled' => 'required|boolean',
            'cnpj_enabled' => 'required|boolean',
            'credit_card_enabled' => 'required|boolean',
            'bank_slip_enabled' => 'required|boolean',
            'pix_enabled' => 'required|boolean',
            'quantity_selector_enabled' => 'required|boolean',
            'email_required' => 'required|boolean',
            'installments_limit' => 'required_if:credit_card_enabled,1|integer',
            'interest_free_installments' => 'required_if:credit_card_enabled,1|integer',
            'preselected_installment' => 'required_if:credit_card_enabled,1|integer',
            'bank_slip_due_days' => 'required_if:bank_slip_enabled,1|integer',
            'automatic_discount_credit_card' => 'required_if:credit_card_enabled,1|integer',
            'automatic_discount_bank_slip' => 'required_if:bank_slip_enabled,1|integer',
            'automatic_discount_pix' => 'required_if:pix_enabled,1|integer',
            'post_purchase_message_enabled' => 'required|boolean',
            'post_purchase_message_title' => 'required_if:post_purchase_message_enabled,1|string|nullable',
            'post_purchase_message_content' => 'required_if:post_purchase_message_enabled,1|string|nullable',
            'whatsapp_enabled' => 'required|boolean',
            'support_phone' => 'string|nullable',
            'theme_enum' => 'nullable|integer',
            'color_primary' => 'required|string',
            'color_secondary' => 'required|string',
            'color_buy_button' => 'required|string',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
