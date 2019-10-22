<?php

namespace Modules\Plans\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'project_id'      => 'nullable',
            'name'            => 'required|max:200',
            'price'           => 'required',
            'description'     => 'nullable|max:200',
            'products'        => 'required|array',
            'product_amounts' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'name.required'            => 'O campo Nome é obrigatório',
            'price.required'           => 'O campo Preço é obrigatório',
            'description.required'     => 'O campo Descrição é obrigatório',
            'products.required'        => 'O campo Produtos de origem é obrigatório',
            'product_amounts.required' => 'O campo Quantidade é obrigatório',
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
