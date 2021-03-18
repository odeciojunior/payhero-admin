<?php

namespace Modules\Pixels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PixelStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:100',
            'platform' => 'required',
            'status' => 'nullable',
            'code' => 'required',
            'api-facebook' => 'nullable|string',
            'facebook-token-api' => 'nullable|string',
            'purchase_event_name' => 'nullable|max:255',
            'add_pixel_plans' => 'required|array',
            'checkout' => 'nullable',
            'purchase_card' => 'nullable',
            'purchase_boleto' => 'nullable',
            'campaign' => 'nullable',
            'affiliate_id' => 'nullable',
            'value_percentage_purchase_boleto' => 'nullable|integer|max:100|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo Descrição é obrigatório',
            'name.max' => 'O campo Descrição permite apenas 100 caracteres',
            'code.required' => 'O campo Código é obrigatório',
            'platform.required' => 'O campo Plataforma é obrigatório',
            'add_pixel_plans.required' => 'É obrigatório selecionar um ou mais planos',
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
