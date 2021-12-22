<?php

namespace Modules\CheckoutEditor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCheckoutConfigRequest extends FormRequest
{
    public function rules()
    {
        return [
            'project_id' => 'required|string',
            'checkout_type_enum' => 'required|integer',
            'checkout_logo' => 'nullable|file',
            'checkout_banner_enabled' => 'required|boolean',
            'checkout_banner_type' => 'required_if:checkout_banner_enabled|integer',
            'checkout_banner' => 'required_if:checkout_banner_enabled|string',
            'countdown_enabled' => 'required|boolean',
            'countdown_time' => 'required_if:countdown_enabled|integer',
            'countdown_description' => 'required_if:countdown_enabled|string',
            'countdown_finish_message' => 'required_if:countdown_enabled|string',
            'topbar_enabled' => 'required|boolean',
            'topbar_content' => 'required_if:topbar_enabled|string',
            'notifications_enabled' => 'required|boolean',
            'notifications_interval' => 'required_if:notifications_enabled|integer',
            'notification_buying_enabled' => 'required_if:notifications_enabled|boolean',
            'notification_buying_minimum' => 'required_if:notification_buying_enabled|integer',
            'notification_bought_30_minutes_enabled' => 'required_if:notifications_enabled|boolean',
            'notification_bought_30_minutes_minimum' => 'required_if:notification_bought_30_minutes_enabled|integer',
            'notification_bought_last_hour_enabled' => 'required_if:notifications_enabled|boolean',
            'notification_bought_last_hour_minimum' => 'required_if:notification_bought_last_hour_enabled|integer',
            'notification_just_bought_enabled' => 'required_if:notifications_enabled|boolean',
            'notification_just_bought_minimum' => 'required_if:notification_just_bought_enabled|integer',
            'social_proof_enabled' => 'required|boolean',
            'social_proof_message' => 'required_if:social_proof_enabled|string',
            'social_proof_minimum' => 'required_if:social_proof_enabled|integer',
            'invoice_description' => 'nullable|string',
            'company_id' => 'required|string',
            'cpf_enabled' => 'required|boolean',
            'cnpj_enabled' => 'required|boolean',
            'credit_card_enabled' => 'required|boolean',
            'bank_slip_enabled' => 'required|boolean',
            'pix_enabled' => 'required|boolean',
            'quantity_selector_enabled' => 'required|boolean',
            'email_required' => 'required|boolean',
            'installments_limit' => 'required_if:credit_card_enabled|integer',
            'interest_free_installments' => 'required_if:credit_card_enabled|integer',
            'preselected_installment' => 'required_if:credit_card_enabled|integer',
            'bank_slip_due_days' => 'required_if:bank_slip_enabled|integer',
            'automatic_discount_credit_card' => 'required_if:credit_card_enabled|integer',
            'automatic_discount_bank_slip' => 'required_if:bank_slip_enabled|integer',
            'automatic_discount_pix' => 'required_if:pix_enabled|integer',
            'post_purchase_message_enabled' => 'required|boolean',
            'post_purchase_message_title' => 'required_if:post_purchase_message_enabled|string',
            'post_purchase_message_content' => 'required_if:post_purchase_message_enabled|string',
            'whatsapp_enabled' => 'required|boolean',
            'support_phone' => 'required_if:whatsapp_enabled|string',
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
