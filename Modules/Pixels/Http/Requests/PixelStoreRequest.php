<?php

namespace Modules\Pixels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PixelStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'campaign' => 'nullable',
            'name' => 'required|max:100',
            'code' => 'required',
            'platform' => 'required',
            'status' => 'nullable',
            'checkout' => 'nullable',
            'purchase_boleto' => 'nullable',
            'purchase_card' => 'nullable',
            'affiliate_id' => 'nullable',
            'add_pixel_plans' => 'required|array',
            'code_meta_tag_facebook' => 'nullable|string|max:255',
            'purchase_event_name' => 'nullable|max:255',
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
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
