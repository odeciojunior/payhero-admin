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
            'project_id'      => 'required',
            'name'            => 'required|max:50',
            'price'           => 'required',
            'description'     => 'nullable|max:50',
            'products'        => 'required|array',
            'product_amounts' => 'required|array',
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
            'name.required'            => 'O campo Nome é obrigatório',
            'price.required'           => 'O campo Preço é obrigatório',
            'description.required'     => 'O campo Descrição é obrigatório',
            'products.required'        => 'O campo Produto é obrigatório',
            'product_amounts.required' => 'O campo Quantidade é obrigatório',
            'description.max'          => 'O campo Descrição permite apenas 30 caracteres',

        ];
    }
}
