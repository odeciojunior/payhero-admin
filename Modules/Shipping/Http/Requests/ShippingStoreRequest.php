<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ShippingStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $isStatic = $this->get('type') == 'static';
        $isMenv = Str::contains($this->get('type'), 'melhorenvio');

        return [
            "type"               => "required|string",
            "name"               => "required|string|max:60",
            "information"        => $isMenv ? "" : "required|string|max:100",
            "value"              => "nullable",
            "zip_code_origin"    => $isStatic ? "" : "required|min:9",
            "status"             => "nullable",
            "pre_selected"       => "nullable",
            "use_variants"       => "nullable",
            "rule_value"         => "nullable",
            "regions_values"     => "nullable",
            "apply_on_plans"     => "required|array",
            "not_apply_on_plans" => "sometimes|required|array"
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
            'zip_code_origin.required' => 'O campo CEP de origem é obrigatório',
            'zip_code_origin.min'      => 'O campo CEP de origem é deve ter no mínimo 8 dígitos',
            'status.required'          => 'O campo status é obrigatório',
            'pre_selected.required'    => 'O campo Pré-selecionado é obrigatório',
            'value.required'           => 'O campo valor é obrigatório',
            'value.max'                => 'O campo Valor do Frete permite apenas 30 caracteres',
            'apply_on_plans.required'  => 'Necessário informar para quais planos o frete estará disponível'
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
