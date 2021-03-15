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
            'purchase_boleto' => 'nullable',
            'purchase_card' => 'nullable',
            'project_id' => 'nullable',
            'campaign' => 'nullable',
            'purchase_event_name' => 'nullable|max:255',
            'is_api' => 'nullable|string',
            'facebook_token_api' => 'nullable|string',
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
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
