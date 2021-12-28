<?php

namespace Modules\Projects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "photo_x1" => "nullable",
            "photo_y1" => "nullable",
            "photo_w" => "nullable",
            "photo_h" => "nullable",
            "name" => "nullable|string|max:100",
            "description" => "nullable|string|max:255",
            "visibility" => "nullable",
            "url_page" => "nullable|string|max:255",
            "contact" => "nullable",
            "invoice_description" => "nullable",
            "boleto_redirect" => "nullable",
            "card_redirect" => "nullable",
            "pix_redirect" => "nullable",
            "analyzing_redirect" => "nullable",
            "company_id" => "nullable",
            "installments_amount" => "nullable",
            "installments_interest_free" => "nullable",
            "boleto" => "nullable",
            "credit_card" => "nullable",
            "pix" => "nullable",
            "boleto_due_days" => "nullable",
            "logo_x1" => "nullable",
            "logo_y1" => "nullable",
            "logo_w" => "nullable",
            "logo_h" => "nullable",
            "ratioImage" => "nullable",
            "photo" => 'nullable',
            "logo" => 'nullable',
            "support_phone" => 'nullable',
            "discount_recovery_status" => 'nullable',
            "discount_recovery_value" => 'nullable',
            // "cost_currency_type" => 'required|string|max:5',
            "checkout_type" => 'required',
            "terms_affiliates" => 'nullable',
            "percentage_affiliates" => 'nullable',
            "cookie_duration" => 'nullable',
            "automatic_affiliation" => 'nullable',
            "status_url_affiliates" => 'nullable|int|max:1',
            "commission_type_enum" => 'nullable|int|max:2',
            "whatsapp_button" => 'nullable',
            "credit_card_discount" => 'int|max:100',
            "billet_discount" => 'int|max:100',
            "pix_discount" => 'int|max:100',
            "pre_selected_installment" => 'nullable',
            "required_email_checkout" => 'nullable',
            "document_type_checkout" => 'nullable',
            'countdown_timer_flag' => 'boolean',
            'countdown_timer_color' => 'string|max:7',
            'countdown_timer_time' => 'int|min:1',
            'countdown_timer_description' => 'nullable|string|max:255',
            'product_amount_selector' => 'boolean',
            'finalizing_purchase_config_toogle' => 'nullable|boolean',
            'finalizing_purchase_config_text' => 'required_if:finalizing_purchase_config_toogle,1|string|templateStringMinVisitorInFinalizingPurchaseConfig',
            'finalizing_purchase_config_min_value' => 'required_if:finalizing_purchase_config_toogle,1|digits_between:1,9999999',
            'countdown_timer_finished_message' => 'required|min:20|max:255',
            'checkout_notification_config_toogle' => 'nullable|boolean',
            'checkout_notification_config_time' => 'required_if:checkout_notification_config_toogle,1|digits_between:1,9999999',
            'checkout_notification_mobile' =>  'required_if:checkout_notification_config_toogle,1|boolean',
            'checkout_notification_config_messages' =>  'required_if:checkout_notification_config_toogle,1|array',
            'checkout_notification_config_messages_min_value' => 'required_if:checkout_notification_config_toogle,1|array',
            'custom_message_switch'=>'boolean',
            'custom_message_title'=>'required_if:custom_message_switch,1',
            'custom_message_content'=>'required_if:custom_message_switch,1'
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'O campo Nome do projeto permite apenas 100 caracteres',
            'description.max' => 'O campo Descrição permite apenas 100 caracteres',
            'url_page.max' => 'O campo URL da pagina principal permite apenas 100 caracteres',
            'countdown_timer_time.min' => 'O campo Tempo do Contador precisa ser maior que 0',
            'countdown_timer_finished_message.min' => 'O campo da mensagem ao finalizar o contador precisa ter entre 20 e 255 caracteres',
            'countdown_timer_finished_message.string' => 'O campo da mensagem ao finalizar o contador não pode estar vazio',
            'countdown_timer_finished_message.required' => 'O campo da mensagem ao finalizar o contador não pode estar vazio',
            'finalizing_purchase_config_text.required_if' => 'Campo obrigatório enquanto a opção pessoas finalizando compra estiver ativo.',
            'finalizing_purchase_config_min_value.required_if' => 'Campo obrigatório enquanto a opção pessoas finalizando compra estiver ativo.',
            'checkout_notification_config_time.required_if' =>  'Campo obrigatório enquanto a opção mostrar notificação de vendas estiver ativo.',
            'checkout_notification_mobile.required_if' =>  'Campo obrigatório enquanto a opção mostrar notificação de vendas estiver ativo.',
            'checkout_notification_config_messages.required_if' =>  'Campo obrigatório enquanto a opção mostrar notificação de vendas estiver ativo.',
            'checkout_notification_config_messages_min_value.required_if' =>  'Campo obrigatório enquanto a opção mostrar notificação de vendas estiver ativo.',            
            'custom_message_title.required_if'=>'Informe o titulo da mensagem personalizada',
            'custom_message_content.required_if'=>'Informe a mensagem personalizada'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
