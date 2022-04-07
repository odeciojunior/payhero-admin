<?php

namespace Modules\Pixels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PixelUpdateRequest
 * @package Modules\Pixels\Http\Requests
 */
class PixelUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:100|string',
            'platform' => 'required',
            'status' => 'nullable',
            'code' => 'required|string',
            'edit_pixel_plans' => 'required|array',
            'checkout' => 'nullable',
            'send_value_checkout' => 'nullable',
            'purchase_all' => 'nullable',
            'basic_data' => 'nullable',
            'delivery' => 'nullable',
            'coupon' => 'nullable',
            'payment_info' => 'nullable',
            'purchase_card' => 'nullable',
            'purchase_boleto' => 'nullable',
            'purchase_pix' => 'nullable',
            'upsell' => 'nullable',
            'purchase_upsell' => 'nullable',
            'campaign' => 'nullable',
            'purchase_event_name' => 'nullable|max:255',
            'is_api' => 'nullable|string',
            'facebook_token_api' => 'nullable|string',
            'value_percentage_purchase_boleto' => 'nullable|integer|max:100|min:10',
            'url_facebook_domain_edit' => 'nullable|string|max:255',
            'event_select' => 'required_if:platform,google_adwords',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo Descrição é obrigatório',
            'name.max' => 'O campo Descrição permite apenas 100 caracteres',
            'code.required' => 'O campo Código é obrigatório',
            'platform.required' => 'O campo Plataforma é obrigatório',
            'edit_pixel_plans.required' => 'O campo Plano é obrigatório',
            'value_percentage_purchase_boleto.integer' => 'O campo % Valor Boleto deve ser um número',
            'value_percentage_purchase_boleto.min' => 'O valor do campo % Valor Boleto deve ser no mínimo 10',
            'value_percentage_purchase_boleto.max' => 'O valor do campo % Valor Boleto deve ser no máximo 100',
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
