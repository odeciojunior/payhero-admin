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
            "name"            => "required|string|max:60",
            "information"     => "required|string|max:100",
            "value"           => "nullable|string|max:8",
            "zip_code_origin" => "nullable|string",
            "status"          => "nullable",
            "pre_selected"    => "nullable",
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'type.required'            => 'O campo tipo é obrigatório',
            'name.required'            => 'O campo descrição é obrigatório',
            'name.max'                 => 'O campo descrição permite apenas 100 caracteres',
            'information.required'     => 'O campo Tempo de entrega é obrigatório',
            'information.max'          => 'O campo Tempo de entrega permite apenas 30 caracteres',
            'zip_code_origin.required' => 'O campo código de origem é obrigatório',
            'status.required'          => 'O campo status é obrigatório',
            'pre_selected.required'    => 'O campo Pré-selecionado é obrigatório',
            'value.max'                => 'O campo Valor do Frete permite apenas 8 caracteres',
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
