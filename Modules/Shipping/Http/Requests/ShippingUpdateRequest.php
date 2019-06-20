<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            "type"            => "required|string",
            "name"            => "required|string",
            "information"     => "required|string",
            "value"           => "nullable|string",
            "zip_code_origin" => "required|string",
            "status"          => "required",
            "pre_selected"    => "required",
        ];
    }

    public function messages()
    {
        return [
            'type.required'            => 'O campo tipo é obrigatório',
            'name.required'            => 'O campo descrição é obrigatório',
            'information.required'     => 'O campo Informação é obrigatório',
            'zip_code_origin.required' => 'O campo código de origem é obrigatório',
            'status.required'          => 'O campo status é obrigatório',
            'pre_selected.required'    => 'O campo Pré-selecionado é obrigatório',
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
