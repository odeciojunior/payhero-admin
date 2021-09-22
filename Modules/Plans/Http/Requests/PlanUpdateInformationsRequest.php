<?php

namespace Modules\Plans\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanUpdateInformationsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'            => 'required|max:200',
            'price'           => 'required',
            'description'     => 'nullable|max:200',
        ];
    }

    public function messages()
    {
        return [
            'name.required'            => 'O campo Nome é obrigatório',
            'price.required'           => 'O campo Preço é obrigatório',
            'description.required'     => 'O campo Descrição é obrigatório',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
