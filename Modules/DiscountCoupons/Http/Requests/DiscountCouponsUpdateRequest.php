<?php

namespace Modules\DiscountCoupons\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountCouponsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'name'              => 'nullable',
            'type'              => 'nullable',
            'value'             => 'nullable',
            'code'              => 'nullable',
            'status'            => 'nullable',
            'rule_value'        => 'nullable',
            'discount'          => 'nullable',
            'plans'             => 'nullable',
            'progressive_rules' => 'nullable',
            'expires'          => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required'  => 'O campo Nome é obrigatório',
            'name.max'       => 'O campo Nome permite apenas 100 caracteres',
            'type.required'  => 'O campo Tipo é obrigatório',
            'value.required' => 'O campo Valor é obrigatório',
            'value.max'      => 'O campo Valor permite apenas 30 caracteres ',
            //'code.required'  => 'O campo Código de origem é obrigatório',
            //'code.max'       => 'O campo Código permite apenas 30 caracteres',
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
