<?php

namespace Modules\Pixels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PixelStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => "required|max:30",
            "platform" => "required",
            "status" => "nullable",
            "code" => "required|string|max:100",
            "api-facebook" => "nullable|string",
            "facebook-token-api" => "nullable|string",
            "purchase-event-name" => "nullable|max:255",
            "add_pixel_plans" => "required|array",
            "checkout" => "nullable",
            "purchase_all" => "nullable",
            "basic_data" => "nullable",
            "delivery" => "nullable",
            "coupon" => "nullable",
            "payment_info" => "nullable",
            "purchase_card" => "nullable",
            "purchase_boleto" => "nullable",
            "purchase_pix" => "nullable",
            "upsell" => "nullable",
            "purchase_upsell" => "nullable",
            "campaign" => "nullable",
            "affiliate_id" => "nullable",
            "value_percentage_purchase_boleto" => "nullable|integer|max:100|min:10",
            "url_facebook_domain" => "nullable|string|max:255",
            "event_select" => "required_if:platform,google_adwords",
            "send_value_checkout" => "nullable",
        ];
    }

    public function messages(): array
    {
        return [
            "name.required" => "O campo Descrição é obrigatório",
            "name.max" => "O campo Descrição permite apenas 30 caracteres",
            "code.required" => "O campo Código é obrigatório",
            "code.max" => "O campo Código permite no maximo 100 caracteres",
            "platform.required" => "O campo Plataforma é obrigatório",
            "add_pixel_plans.required" => "É obrigatório selecionar um ou mais planos",
            "value_percentage_purchase_boleto.integer" => "O campo % Valor Boleto deve ser um número",
            "value_percentage_purchase_boleto.min" => "O valor do campo % Valor Boleto deve ser no mínimo 10",
            "value_percentage_purchase_boleto.max" => "O valor do campo % Valor Boleto deve ser no máximo 100",
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
