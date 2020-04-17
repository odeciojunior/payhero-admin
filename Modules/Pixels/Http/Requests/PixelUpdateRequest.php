<?php

namespace Modules\Pixels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PixelUpdateRequest
 * @package Modules\Pixels\Http\Requests
 */
class PixelUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'name'             => 'required|max:100|string',
            'platform'         => 'required',
            'status'           => 'nullable',
            'code'             => 'required|string',
            'edit_pixel_plans' => 'required|array',
            'checkout'         => 'nullable',
            'purchase_boleto'  => 'nullable',
            'purchase_card'    => 'nullable',
            'project_id'       => 'nullable',
            'campaign'         => 'nullable',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'name.required'             => 'O campo Descrição é obrigatório',
            'name.max'                  => 'O campo Descrição permite apenas 100 caracteres',
            'code.required'             => 'O campo Código é obrigatório',
            'platform.required'         => 'O campo Plataforma é obrigatório',
            'edit_pixel_plans.required' => 'O campo Plano é obrigatório',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
