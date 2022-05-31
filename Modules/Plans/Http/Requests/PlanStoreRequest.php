<?php

namespace Modules\Plans\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class PlanStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'project_id'                    => 'required',
            'name'                          => 'required|max:50',
            'price'                         => 'required',
            'description'                   => 'nullable|max:50',
            'products'                      => 'required',
            'products.*.amount'             => 'required',
            'products.*.currency_type_enum' => 'required',
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

    public function messages()
    {
        return [
            'name.required'                 => 'O campo Nome é obrigatório',
            'price.required'                => 'O campo Preço é obrigatório',
            'description.required'          => 'O campo Descrição é obrigatório',
            'description.max'               => 'O campo Descrição permite apenas 30 caracteres',
            'products.required'             => 'O campo Produto é obrigatório',
            'products.*.amount.required'    => 'O campo Quantidade é obrigatório',
            'products.*.currency_type_enum' => 'O campo Moeda é obrigatório'

        ];
    }
}
