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
            'project' => 'nullable',
            'name'    => 'required',
            'type'    => 'required',
            'value'   => 'required',
            'code'    => 'required',
            'status'  => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required'  => 'O campo Nome é obrigatório',
            'type.required'  => 'O campo Tipo é obrigatório',
            'value.required' => 'O campo Valor é obrigatório',
            'code.required'  => 'O campo Código de origem é obrigatório',
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
